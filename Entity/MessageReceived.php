<?php
namespace Sopinet\ChatBundle\Entity;
use Sopinet\ChatBundle\Entity\Message;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class MessageReceived extends Message
{
    public function getMyIOSContentAvailable() {
        return false;
    }
}