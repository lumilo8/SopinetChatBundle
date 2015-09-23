<?php
namespace Sopinet\ChatBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\EntityRepository;
use Sopinet\ChatBundle\Entity\Chat;
use Sopinet\ChatBundle\Entity\Message;
use Sopinet\ChatBundle\Model\MessageObject;
use Sopinet\ChatBundle\Entity\Device;

class MessageRepository extends EntityRepository{
    public function getTypesMessage() {
        $messages = $this->findAll();
        $types = array();
        /** @var Message $message */
        foreach($messages as $message) {
            if (!in_array($message->getTypeClient(), $types)) {
                $types[$message->getTypeClient()] = $message->getTypeClient();
            }
        }
        return $types;
    }
}