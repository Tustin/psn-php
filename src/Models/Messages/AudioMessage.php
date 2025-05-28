<?php

namespace Tustin\PlayStation\Models\Messages;

use Tustin\PlayStation\Enums\MessageType;
use Tustin\PlayStation\Models\Message\AbstractMessage;

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
