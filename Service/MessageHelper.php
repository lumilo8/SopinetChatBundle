<?php

namespace Sopinet\ChatBundle\Service;

use Application\Sonata\UserBundle\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Mapping\MetadataFactory;
use FOS\RestBundle\View\ViewHandler;
use FOS\UserBundle\Document\UserManager;
use Sonata\CoreBundle\Model\Metadata;
use Sopinet\ChatBundle\Entity\Chat;
use Sopinet\ChatBundle\Entity\ChatRepository;
use Sopinet\ChatBundle\Entity\Message;
use Sopinet\ChatBundle\Entity\Device;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\View\View;
use Doctrine\ORM\EntityManager;
use JMS\Serializer\SerializationContext;
use Symfony\Component\Security\Acl\Exception\Exception;
use RMS\PushNotificationsBundle\Exception\InvalidMessageTypeException;
use RMS\PushNotificationsBundle\Message\AndroidMessage;
use RMS\PushNotificationsBundle\Message\iOSMessage;
use Sopinet\ChatBundle\Entity\DeviceRepository;
use Sopinet\ChatBundle\Entity\MessagePackage;
use Sopinet\ChatBundle\Form\DeviceType;
use Sopinet\ChatBundle\Model\MinimalPackage;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;

/**
 * Class MessageHelper
 * @package Sopinet\ChatBundle\Service
 */
class MessageHelper {
    public function __construct(Container $container) {
        $this->container = $container;
    }

    /**
     * Get Message class String from $type
     *
     * @param $type
     * @return null|string
     */
    public function getMessageClassString($type) {
        $em = $this->container->get('doctrine')->getManager();

        $messageClass = "Sopinet\ChatBundle\Entity\Message";

        $cmf = $em->getMetadataFactory();
        $meta = $cmf->getMetadataFor($messageClass);

        $config = $this->container->getParameter('sopinet_chat.config');
        foreach($meta->discriminatorMap as $typeString => $typeClass) {
            if ($typeString == $type) {
                if (!$config['all_type_message'][$type]['interfaceEnabled']) {
                    // Type forbidden, interfaceEnabled for this type is setted in False
                    return;
                } else {
                    return $typeClass;
                }
            }
        }

        if ($config['anyType'] && $type != "") {
            return "Sopinet\ChatBundle\Entity\MessageAny";
        }

        // Type unknow
        return;
    }

    /**
     * Get Message class Object from $type
     *
     * @param $type
     * @return mixed
     * @throw If not found type
     */
    public function getMessageClassObject($type) {
        // Type is obligatory // TODO: Configurate obligatory?
        if ($type == null) throw new Exception("Error Type is null");

        $messageClassString = $this->getMessageClassString($type);
        if ($messageClassString == null) {
            throw new Exception("Error Type");
        }

        $messageClassObject = new $messageClassString;
        if ($messageClassObject->getMyType() == "any") {
            $messageClassObject->setAnyType($type);
            $messageClassObject->setTypeClient($type);
        }

        return $messageClassObject;
    }

    /**
     * Manda un mensaje a los usuarios que el tipo de Mensaje indique
     *
     * @param Message $message
     * @return integer: -1 si hay error, 0 si no hay dispositivos a los que mandar, y más de 0 indicando el número de mensajes enviados
     */
    public function sendMessage(Message $message) {
        $users = $message->getMyDestionationUsers($this->container);
        $sentCount = 0;
        foreach($users as $user) {
            $sentCount += $this->sendMessageToUser($message, $user);
        }
        return $sentCount;
    }

    public function sendMessageToUser(Message $message, User $user) {
        $sentCount = 0;
        $em = $this->container->get('doctrine.orm.default_entity_manager');
        /** @var Device $device */
        foreach($user->getDevices() as $device) {
            if ($message->getFromDevice() == null || $message->getFromDevice()->getDeviceId() != $device->getDeviceId()) {
                // DEPRECATED! Next code is deprecated, now i pass message object for better iOS options
                //$messageObject = $message->getMyMessageObject($this->container);
                //$text = $message;
                $response = $this->sendRealMessageToDevice($message, $device, $user);
                $messagePackage = new MessagePackage();
                $messagePackage->setMessage($message);
                $messagePackage->setToDevice($device);
                $messagePackage->setToUser($user);
                if ($response) {
                    $messagePackage->setStatus(MessagePackage::STATUS_OK);
                } else {
                    $messagePackage->setStatus(MessagePackage::STATUS_KO);
                }
                if ($device->getDeviceType() == Device::TYPE_ANDROID) {
                    $messagePackage->setProcessed(true); // Yes, processed
                } elseif ($device->getDeviceType() == Device::TYPE_IOS) {
                    $messagePackage->setProcessed(false); // Not processed
                }
                $em->persist($messagePackage);
                $em->flush();
                $sentCount++;
            }
        }
        return $sentCount;
    }

    /**
     * Send message to User and device
     * It functions transform to Real message data array
     *
     * @param Object $msg
     * @param String $to
     *
     */
    public function sendRealMessageToDevice(Message $message, Device $device, User $user = null)
    {
        $config = $this->container->getParameter('sopinet_chat.config');

        $messageData = $message->getMyMessageObject($this->container);

        $text = $message->__toString();

        $messageArray = array();

        $vars = get_object_vars($messageData);

        foreach($vars as $key => $value) {
            $messageArray[$key] = $value;
        }

        if ($user != null) {
            $messageArray['toUserId'] = $user->getId();
        }

        if ($device->getDeviceType() == Device::TYPE_ANDROID && $config['enabledAndroid']) {
            return $this->sendGCMessage($messageArray, $text, $device->getDeviceGCMId());
        } elseif ($device->getDeviceType() == Device::TYPE_IOS && $config['enabledIOS']) {
            return $this->sendAPNMessage($messageArray, $text, $device->getDeviceId(), $message->getMyIOSContentAvailable(), $message->getMyIOSNotificationFields());
        }
    }

    /**
     * Funcion que envia un mensaje con el servicio GCM de Google
     * @param Array $mes
     * @param DeviceId $to
     */
    private function sendGCMessage($mes, $text, $to)
    {
        $message=new AndroidMessage();
        $message->setMessage($text);
        $message->setData($mes);
        $message->setDeviceIdentifier($to);
        $message->setGCM(true);
        $logger = $this->container->get('logger');
        $logger->emerg(implode(',', $message->getData()));
        try {
            $response = $this->container->get('rms_push_notifications')->send($message);
        } catch (InvalidMessageTypeException $e) {
            throw $e;
        }

        return $response;
    }


    /**
     * Funcion que envia un mensaje con el sevricio APN de Apple
     * @param Array $mes, Array of parameters, it should have 'type'
     * @param String $text
     * @param String $to
     * @param boolean $wakeUp - boolean parameter, if true: it send normal message, if false it send notification message.
     * It shoud be replace by typemessage
     *
     * @throws \InvalidArgumentException
     */
    private function sendAPNMessage($mes, $text, $to, $contentAvailable, $notificationFields)
    {
        $message=new iOSMessage();
        try {
            $message->setData($mes);
        } catch (\InvalidArgumentException $e) {
            throw $e;
        }
        $alert=[];
        $logger = $this->container->get('logger');
        $logger->emerg(implode(',', $mes));

        // - $em = $this->container->get("doctrine.orm.entity_manager");
        // - $reDevice = $em->getRepository('ApplicationSopinetUserBundle:User');

        /** @var User $user */
        // $user=$reDevice->findOneByPhone($mes['phone']);
        if ($contentAvailable) {
            /**
            if ($mes['chattype']=='event') {
                $em = $this->container->get("doctrine.orm.entity_manager");
                $reChat = $em->getRepository('PetyCashAppBundle:Chat');
                $chat = $reChat->find($mes['chatid']);
                $text = $chat->getName().'@'.$user->getUserName();
            } else {
                $text = $user->getUserName();
            }
            **/

            //nombredelChat@nombredeUsuario;
            $messageString = $notificationFields;
            $alert['loc-args'] = array($messageString, $text);
            $alert['loc-key']=$mes['type'];
            $message->setMessage($alert);
            $config = $this->container->getParameter('sopinet_chat.config');
            $message->setAPSSound($config['soundIOS']);

        }
        $message->setDeviceIdentifier($to);
        $message->setAPSContentAvailable($contentAvailable);
        return $this->container->get('rms_push_notifications')->send($message);
    }
}