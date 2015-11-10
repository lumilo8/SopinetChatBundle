<?php
namespace Sopinet\ChatBundle\Entity;
use Sopinet\ChatBundle\Entity\Message;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ORM\Entity
 */
class MessageAny extends Message
{
    /**
     * @ORM\Column(name="anyType", type="string")
     */
    protected $anyType;

    public function setAnyType($anyType)
    {
        $this->anyType = $anyType;

        return $this;
    }

    public function getAnyType()
    {
        return $this->anyType;
    }

    public function getMyForm() {
        return "\Sopinet\ChatBundle\Form\MessageType";
    }

    public function getMyMessageObject($container, $request = NULL){
        $messageObject = parent::getMyMessageObject($container);

        $messageObject->type = $this->anyType;

        return $messageObject;
    }

    /**
     * Set subject
     *
     * @param integer $subject
     * @return MessageAny
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
