<?php

namespace Tustin\PlayStation\Models\Messages;

use Tustin\PlayStation\Models\Media;
use Tustin\PlayStation\Enums\MessageType;

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
