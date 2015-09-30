<?php
namespace Sopinet\ChatBundle\Entity;
use Sopinet\ChatBundle\Entity\Message;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class MessageText extends Message
{
    public function getMyIOSNotificationFields() {
        $userName = $this->getFromUser()->__toString();
        $text = $this->__toString();

        return $userName . "@" . $text;
    }

    /** ESTO NO SE USA, NO???
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    public function getSubject()
    {
        return $this->subject;
    }
    **/
}
