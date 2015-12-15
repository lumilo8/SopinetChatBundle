<?php
namespace Sopinet\ChatBundle\Entity;

use Application\Sonata\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model as ORMBehaviors;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\Exclude;
use Gedmo\Mapping\Annotation as Gedmo;
use Sopinet\ChatBundle\Model\MessageObject;
use Sopinet\ChatBundle as Chat;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\DependencyInjection\Container;

/**
 * Entity Message
 *
 * @ORM\Entity(repositoryClass="Sopinet\ChatBundle\Entity\MessageRepository")
 * @DoctrineAssert\UniqueEntity("id")
 * @ORM\Table(name="sopinet_chatbundle_message")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"message" = "Message"})
 */
abstract class Message
{
    use Chat\Model\MinimalPackage;
    use ORMBehaviors\Timestampable\Timestampable;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="string")
     * @ORM\GeneratedValue(strategy="NONE")
     * @Groups({"create"})
     * @ORM\Id
     *
     * Este ID será único, formado por un md5 de: Secret + messageIdLocal + deviceId
     *
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Chat", inversedBy="messages", cascade={"persist"})
     * @ORM\JoinColumn(name="chat_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     * @ORM\OrderBy({"id" = "DESC"})
     */
    protected $chat;

    /**
     * @ORM\OneToMany(targetEntity="MessagePackage", mappedBy="message", cascade={"persist"})
     */
    protected $messagesGenerated;

    /**
     * @ORM\ManyToOne(targetEntity="\Application\Sonata\UserBundle\Entity\User", inversedBy="messages", cascade={"persist"})
     * @ORM\JoinColumn(name="fromUser_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     * @ORM\OrderBy({"id" = "DESC"})
     * @Exclude
     */
    protected $fromUser;

    /**
     * @var datetime
     *
     * @ORM\Column(name="fromTime", type="datetime")
     */
    protected $fromTime;

    /**
     * @ORM\Column(name="typeClient", type="string")
     */
    protected $typeClient;

    /**
     * @ORM\Column(name="read", type="boolean", nullable=true)
     */
    protected $read;

    public function setTypeClient($typeClient) {
        $this->typeClient = $typeClient;
    }

    public function getTypeClient() {
        return $this->typeClient;
    }

    /**
     * Set fromTime
     *
     * @param \DateTime $fromTime
     *
     * @return Message
     */
    public function setFromTime(\DateTime $fromTime)
    {
        $this->fromTime = $fromTime;

        return $this;
    }

    /**
     * Get dateSend
     *
     * @return \DateTime
     */
    public function getFromTime()
    {
        return $this->fromTime;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set chat
     *
     * @param \Sopinet\ChatBundle\Entity\Chat $chat
     * @return Message
     */
    public function setChat(\Sopinet\ChatBundle\Entity\Chat $chat = null)
    {
        $this->chat = $chat;

        return $this;
    }

    /**
     * Get chat
     *
     * @return \Sopinet\ChatBundle\Entity\Chat
     */
    public function getChat()
    {
        return $this->chat;
    }

    /**
     * Set fromUser
     *
     * @param \Application\Sonata\UserBundle\Entity\User $fromUser
     * @return Message
     */
    public function setFromUser(\Application\Sonata\UserBundle\Entity\User $fromUser = null)
    {
        $this->fromUser = $fromUser;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Application\Sonata\UserBundle\Entity\User
     */
    public function getFromUser()
    {
        return $this->fromUser;
    }

    /** Set read
     *
     * @param boolean $read
     * @return Message
     */
    public function setRead($read)
    {
        $this->read = $read;

        return $this;
    }

    /**
     * Get read
     *
     * @return boolean
     */
    public function getRead()
    {
        return $this->read;
    }

    /**
     * Convert Message (DataBase) to MessageObject (for send)
     * @return MessageObject
     */
    public function getMyMessageObject($container, $request = null){
        $messageObject = new MessageObject();
        $messageObject->uniqMessageId = $this->getId();
        $messageObject->text = $this->getText();
        $messageObject->type = $this->getMyType();
        $messageObject->fromTime = $this->getFromTime()->getTimestamp();

        // If chat not null
        if ($this->getChat() != null) {
            $messageObject->chatId = $this->getChat()->getId();
        }

        // If device not null
        if ($this->getFromDevice() != null) {
            $messageObject->fromDeviceId = $this->getFromDevice()->getDeviceId();
        }

        // If user not null
        if ($this->getFromUser() != null) {
            $messageObject->fromPhone = $this->getFromUser()->getPhone();
            $messageObject->fromUsername = $this->getFromUser()->__toString();
            if ($this->getFromUser()->getFile() != null) {
                $messageObject->fromUserPicture = $this->getFromUser()->getFile()->getHttpWebPath($container, $request);
            } else {
                // TODO: ¿Default Avatar? ¿In File?
                $messageObject->fromUserPicture = null;
            }
            $messageObject->fromUserId = $this->getFromUser()->getId();
        }

        // TODO: iOS STRING!!!!!

        return $messageObject;
    }

    public function getMyIOSNotificationFields() {
        // by default: username (user String)
        return $this->getFromUser()->__toString();
    }

    public function getMyIOSContentAvailable() {
        // by default: true
        return true;
    }

    /**
     * Get Users for classic Message
     * This method can be override
     * TODO: It could be transform in getDestination, and return Users or Device (for anonymous notification system), review it
     */
    public function getMyDestionationUsers($container) {
        // by default: Get users
        $users = $this->getChat()->getChatMembers();

        return $users;
    }

    /**
     * Return type data
     *
     * @return string
     */
    public function getMyType() {
        $className = get_class($this);
        $classParts = explode("\\", $className);
        $classSingle = $classParts[count($classParts) - 1];
        $classLowSingle = strtolower($classSingle);
        $type = str_replace("message", "", $classLowSingle);

        if (!$type) {
            return "unknown";
        } else {
            return $type;
        }
    }

    /**
     * Return string Form
     *
     * @return string
     */
    public function getMyForm() {
        return "\Sopinet\ChatBundle\Form\MessageType";
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->messagesGenerated = new \Doctrine\Common\Collections\ArrayCollection();
        $this->typeClient = $this->getMyType();
    }

    /**
     * Add messagesGenerated
     *
     * @param \Sopinet\ChatBundle\Entity\MessagePackage $messagesGenerated
     * @return Message
     */
    public function addMessagesGenerated(\Sopinet\ChatBundle\Entity\MessagePackage $messagesGenerated)
    {
        $this->messagesGenerated[] = $messagesGenerated;

        return $this;
    }

    /**
     * Remove messagesGenerated
     *
     * @param \Sopinet\ChatBundle\Entity\MessagePackage $messagesGenerated
     */
    public function removeMessagesGenerated(\Sopinet\ChatBundle\Entity\MessagePackage $messagesGenerated)
    {
        $this->messagesGenerated->removeElement($messagesGenerated);
    }

    /**
     * Get messagesGenerated
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMessagesGenerated()
    {
        return $this->messagesGenerated;
    }
}