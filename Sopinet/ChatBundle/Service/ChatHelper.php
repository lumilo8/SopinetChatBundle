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
 * Class ChatHelper
 * @package Sopinet\ChatBundle\Service
 */
class ChatHelper
{
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Obtiene un Chat si existe
     * Se le pasan los usuarios en Request->get('chatMembers')
     * También se puede pasar el id del chat en Request->get('chat')
     * También se puede pasar como parámetro, en chatID
     *
     * Se le pasa el usuario en Request y se hace un Login
     * TODO: Se debería poder configurar para trabajar con Chat que no haya que recuperar
     *
     * @param $userStringArray
     */
    public function getChatExist($request, $chatID = null) {
        // TODO: Comprobar que el usuario logueado es uno de los que pregunta por el CHAT
        $loginHelper = $this->container->get('sopinet_login_helper');
        try {
            $user = $loginHelper->getUser($request);
        } catch(Exception $e) {
            throw new Exception($e->getMessage());
        }

        $em = $this->container->get('doctrine.orm.default_entity_manager');

        $reChat = $em->getRepository('SopinetChatBundle:Chat');
        if ($chatID != null) {
            $chat = $reChat->find($chatID);
            if ($chat != null) return $chat;
        }

        if ($request->get('chat') != "") {
            $chat = $reChat->find($request->get('chat'));
            if ($chat != null) return $chat;
        }

        /** @var UserManager $userManager */
        $userManager = $this->container->get('fos_user.user_manager');

        $chatMembersString = $request->get('chatMembers');
        $chatMembersArray = explode(',', $chatMembersString);
        $users = array();
        foreach($chatMembersArray as $chatMemberID) {
            $chatMember = $userManager->findUserBy(array('id' => $chatMemberID));
            if ($chatMember == null) return null;
            $users[] = $chatMember;
        }

        /** @var ChatRepository $reChat */
        $reChat = $em->getRepository('SopinetChatBundle:Chat');

        return $reChat->getChatExist($users);
    }
}