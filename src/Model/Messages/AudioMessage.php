<?php

namespace Tustin\PlayStation\Model\Messages;

use Tustin\PlayStation\Enums\MessageType;
use Tustin\PlayStation\Model\Message\AbstractMessage;

class AudioMessage extends Message
{
    /**
     * Gets the message type.
     */
    public function type(): MessageType
    {
        return MessageType::Audio;
    }

    public function fetch(): object
    {
        throw new \BadMethodCallException();
    }
}
