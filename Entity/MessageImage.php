<?php
namespace Sopinet\ChatBundle\Entity;
use Sopinet\ChatBundle\Entity\Message;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Request;

/**
 * @ORM\Entity
 */
class MessageImage extends Message
{
    /**
     * @ORM\OneToOne(targetEntity="ImageFile", inversedBy="messageImage", cascade={"persist"})
     */
    protected $file;


    /**
     * @return MessageImage
     */
    public function setFile(ImageFile $file = null)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * Get file
     *
     * @return ImageFile
     */
    public function getFile()
    {
        return $this->file;
    }

    public function getMyForm() {
        return "\Sopinet\ChatBundle\Form\MessageImageType";
    }

    /**
     * Set subject
     *
     * @param integer $subject
     * @return MessageImage
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
