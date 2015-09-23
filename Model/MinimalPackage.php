<?php

namespace Sopinet\ChatBundle\Model;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * Message trait.
 *
 * Should be used inside entity, that needs to be one Message.
 */
trait MinimalPackage
{

    /**
     * @var text
     *
     * @ORM\Column(name="text", type="string")
     * @Assert\NotBlank()
     */
    protected $text;

    /**
     * @ORM\ManyToOne(targetEntity="Sopinet\ChatBundle\Entity\Device", inversedBy="messages", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="device_id", referencedColumnName="id", nullable=true)
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $fromDevice;

    /**
     * Set text
     *
     * @param string $text
     *
     * @return Message
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    public function __toString() {
        return $this->getText();
    }

    /**
     * Set fromDevice
     *
     * @param \Sopinet\ChatBundle\Entity\Device $device
     * @return MinimalPackage
     */
    public function setFromDevice(\Sopinet\ChatBundle\Entity\Device $fromDevice = null)
    {
        $this->fromDevice = $fromDevice;

        return $this;
    }

    /**
     * Get fromDevice
     *
     * @return \Sopinet\ChatBundle\Entity\Device
     */
    public function getFromDevice()
    {
        return $this->fromDevice;
    }
}