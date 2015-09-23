<?php
namespace Sopinet\ChatBundle\Entity;
use Sopinet\ChatBundle\Entity\Message;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class MessageText extends Message
{


    /**
     * Set subject
     *
     * @param integer $subject
     * @return MessageText
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return integer 
     */
    public function getSubject()
    {
        return $this->subject;
    }
}
