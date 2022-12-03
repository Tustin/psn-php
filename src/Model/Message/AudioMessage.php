<?php

namespace Tustin\PlayStation\Model\Message;

use Tustin\PlayStation\Enum\MessageType;
use Tustin\PlayStation\Model\Message\AbstractMessage;

class AudioMessage extends AbstractMessage
{
    public function type(): MessageType
    {
        return MessageType::Audio;
    }

	public function fetch(): object
    {
        throw new \BadMethodCallException();
    }
}
