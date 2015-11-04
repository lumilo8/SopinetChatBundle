<?php
namespace Sopinet\ChatBundle\Listener;

use Gos\Bundle\WebSocketBundle\Event\ClientEvent;
use Gos\Bundle\WebSocketBundle\Event\ClientErrorEvent;
use Gos\Bundle\WebSocketBundle\Event\ServerEvent;
use Gos\Bundle\WebSocketBundle\Event\ClientRejectedEvent;
use Sopinet\ChatBundle\Entity\Device;

class WebClientEventListener
{
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Called whenever a client connects
     *
     * @param ClientEvent $event
     */
    public function onClientConnect(ClientEvent $event)
    {
        $conn = $event->getConnection();

        echo $conn->resourceId . " connected" . PHP_EOL;
    }

    /**
     * Called whenever a client disconnects
     *
     * @param ClientEvent $event
     */
    public function onClientDisconnect(ClientEvent $event)
    {
        $conn = $event->getConnection();

        // Eliminar Device WebClient
        $em = $this->container->get('doctrine.orm.default_entity_manager');
        $reDevice = $em->getRepository('SopinetChatBundle:Device');
        $device = $reDevice->findOneByDeviceId($conn->WAMP->sessionId);
        if ($device) {
            echo "Removed device with SessionID: ".$device->getDeviceId() . PHP_EOL;
            $em->remove($device);
            $em->flush();
        } else {
            echo "Device not was registered ¿?" . PHP_EOL;
        }
        //echo $conn->WAMP->sessionId . " sessionId";

        echo $conn->resourceId . " disconnected WE" . PHP_EOL;
    }

    /**
     * Called whenever a client errors
     *
     * @param ClientErrorEvent $event
     */
    public function onClientError(ClientErrorEvent $event)
    {
        $conn = $event->getConnection();
        $e = $event->getException();

        echo "connection error occurred: " . $e->getMessage() . PHP_EOL;
    }

    /**
     * Called whenever server start
     *
     * @param ServentEvent $event
     */
    public function onServerStart(ServerEvent $event)
    {
        $event = $event->getEventLoop();

        // Cuando el servidor se inicie querrá decir que, previamente, estaba parado, por tanto
        // debemos eliminar todos los Clientes Web que tuviésemos almacenados.

        // Eliminar Device WebClient
        $em = $this->container->get('doctrine.orm.default_entity_manager');
        $reDevice = $em->getRepository('SopinetChatBundle:Device');
        $devices = $reDevice->findByDeviceType(Device::TYPE_WEB);
        /** @var Device $device */
        foreach($devices as $device) {
            echo "Removed device with SessionID: ".$device->getDeviceId() . PHP_EOL;
            $em->remove($device);
            $em->flush();
        }

        echo 'Server was successfully started!'. PHP_EOL;
    }

    /**
     * Called whenever client is rejected by application
     *
     * @param ClientRejectedEvent $event
     */
    public function onClientRejected(ClientRejectedEvent $event)
    {
        $origin = $event->getOrigin;

        echo 'Connection rejected from '. $origin . PHP_EOL;
    }
}