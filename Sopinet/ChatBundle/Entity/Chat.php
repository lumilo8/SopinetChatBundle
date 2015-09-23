<?php
namespace Sopinet\ChatBundle\Entity;

use Application\Sonata\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Exclude;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Entity Chat
 *
 * @ORM\Table("sopinet_chatbundle_chat")
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 * @ORM\Entity(repositoryClass="Sopinet\ChatBundle\Entity\ChatRepository")
 */
class Chat
{
    use ORMBehaviors\Timestampable\Timestampable;

    /**
     * @var \DateTime $deletedAt
     *
     * @ORM\Column(name="deleted_at", type="datetime", nullable=true)
     */
    private $deletedAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"create"})
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string")
     */
    protected $name;

    /**
     * @ORM\ManyToMany(targetEntity="\Application\Sonata\UserBundle\Entity\User", mappedBy="chats")
     */
    protected $chatMembers;

    /**
     * Administrador o persona que inicia el Chat
     *
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\UserBundle\Entity\User", inversedBy="chatsOwned", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="admin_id", referencedColumnName="id", nullable=true)
     */
    protected $admin;

    /**
     * @ORM\OneToMany(targetEntity="Message", mappedBy="chat", cascade={"persist", "remove"})
     * @ORM\OrderBy({"id" = "DESC"})
     * @Exclude
     */
    protected $messages;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->name = "";
        $this->chatMembers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->messages = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Chat
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * Add message
     *
     * @param Message $message
     *
     * @return Chat
     */
    public function addMessage(Message $message)
    {
        $this->messages[] = $message;

        return $this;
    }

    /**
     * Remove message
     *
     * @param Message $message
     */
    public function removeMessage(Message $message)
    {
        $this->messages->removeElement($message);
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMessages()
    {
        return $this->messages;
    }

    public function __toString() {
        return $this->getName();
    }

    /**
     * Devuelve el último mensaje del Chat
     */
    public function refreshLastMessage() {
        $this->last_message = $this->getMessages()[0];
        return $this->last_message;
    }

    /**
     * Set deletedAt
     *
     * @param \DateTime $deletedAt
     * @return Chat
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Get deletedAt
     *
     * @return \DateTime
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Add chatMembers
     *
     * @param \Application\Sonata\UserBundle\Entity\User $chatMembers
     * @return Chat
     */
    public function addChatMember(\Application\Sonata\UserBundle\Entity\User $chatMembers)
    {
        $this->chatMembers[] = $chatMembers;
        $chatMembers->addChat($this);

        return $this;
    }

    /**
     * Remove chatMembers
     *
     * @param \Application\Sonata\UserBundle\Entity\User $chatMembers
     */
    public function removeChatMember(\Application\Sonata\UserBundle\Entity\User $chatMembers)
    {
        $this->chatMembers->removeElement($chatMembers);
    }

    /**
     * Get chatMembers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChatMembers()
    {
        return $this->chatMembers;
    }

    /**
     * Set admin
     *
     * @param \Application\Sonata\UserBundle\Entity\User $admin
     * @return Chat
     */
    public function setAdmin(\Application\Sonata\UserBundle\Entity\User $admin = null)
    {
        $this->admin = $admin;

        return $this;
    }

    /**
     * Get admin
     *
     * @return \Application\Sonata\UserBundle\Entity\User
     */
    public function getAdmin()
    {
        return $this->admin;
    }

    /**
     * Devuelve los dispositivos de todos los usuarios
     * vinculados al Chat
     *
     * @return array|bool
     */
    public function getDevices()
    {
        $devices = array();
        foreach ($this->getChatMembers() as $chatMember) {
            /* @var $chatMember User */
            // Devices to Array
            $devicesObject = $chatMember->getDevices();
            foreach ($devicesObject as $do) {
                $devices[] = $do;
            }
        }

        return $devices;
    }
}
