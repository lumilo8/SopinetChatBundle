<?php

namespace Sopinet\ChatBundle\Service;

use Sopinet\ChatBundle\Entity\MessageUserState;
use Sopinet\ChatBundle\Entity\UserState;
use Symfony\Component\DependencyInjection\Container;
use Doctrine\ORM\EntityManager;

/**
 * Class UserHelper
 * @package Sopinet\ChatBundle\Service
 */
class UserHelper
{
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Hará un ping en el sistema de manera que se guarde la última hora en la cual
     * el usuario hizo una petición, así como su estado: Conectado
     * En caso de cambiar de estado, se notificará a los demás usuarios del Chat en el que está
     * de que el estado a cambiado a conectado.
     *
     * @param User $user - Usuario sobre el que se hace el ping
     * @return boolean $notify - Devuelve si se notifica (hay cambio), o no.
     */
    public function doPing(User $user) {
        /** @var EntityManager $em */
        $em = $this->container->get('doctrine.orm.default_entity_manager');
        $notify = false;
        $userState = $user->getUserState();

        // Si no existe el registro, se crea
        if ($userState == null) {
            $userState = new UserState();
            $userState->setUser($user);
            $user->setUserState($userState);
            $em->persist($user);
            $em->flush();
            $notify = true;
        } else {
            if ($userState->getState() == UserState::STATE_DISCONNECTED) {
                // Notificar que se vuelve activo
                $notify = true;
            }
        }

        // TODO: Check updateAt?
        $userState->setState(UserState::STATE_CONNECTED);
        $em->persist($userState);
        $em->flush();

        if ($notify) {
            $message = new MessageUserState();
            $message->setFromUser($user);
            $message->setText(UserState::STATE_CONNECTED);
            $message->setFromTime(new \DateTime());
            /** @var MessageHelper $messageHelper */
            $messageHelper = $this->container->get('sopinet_chatbundle_messagehelper');
            $messageHelper->sendMessage($message);
        }

        return $notify;
    }
}