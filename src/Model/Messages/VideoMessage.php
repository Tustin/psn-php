<?php

namespace Tustin\PlayStation\Model\Messages;

use Tustin\PlayStation\Model\Media;
use Tustin\PlayStation\Enums\MessageType;
use Tustin\PlayStation\Model\Message\AbstractMessage;

class VideoMessage extends Message
{
    /**
     * Gets the video media.
     */
    public function video(): Media
    {
        return new Media(
            $this->pluck('messageDetail.videoMessageDetail.ugcId')
        );
    }

    /**
     * Gets the message type.
     */
    public function type(): MessageType
    {
        return MessageType::Video;
    }

    public function fetch(): object
    {
        throw new \BadMethodCallException();
    }
}
