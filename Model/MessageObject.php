<?php
namespace Sopinet\ChatBundle\Model;

use Sopinet\ChatBundle\Entity\Message;

class MessageObject {
    public $text; // Text to send

    public $type; // Type (dinamically)

    public $chatId; // Chat ID

    public $uniqMessageId; // uniqID for Message (secret + localMsgID + deviceID)

    public $fromDeviceId; // From device, ID (deviceToken)

    public $fromPhone; //

    public $fromTime; // from TimeStamp

    public $fromUsername; // from message Username

    public $fromUserPicture; // from Picture User

    public $fromUserId; // user id
}
?>