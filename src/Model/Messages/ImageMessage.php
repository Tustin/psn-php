<?php

namespace Tustin\PlayStation\Model\Messages;

use Tustin\PlayStation\Model\Media;
use Tustin\PlayStation\Enums\MessageType;
use Tustin\PlayStation\Model\Message\AbstractMessage;

class ImageMessage extends Message
{
    /**
     * Gets the image media.
     */
    public function image(): Media
    {
        return new Media(
            $this->imageDetails()['ugcId']
        );
    }

    public function imageDetails(): ?array
    {
        return $this->pluck('messageDetail.imageMessageDetail');
    }

    /**
     * Gets the resource id.
     */
    public function resourceId(): string
    {
        return $this->pluck('messageDetail.imageMessageDetail.resourceId');
    }

    /**
     * Gets the message type.
     */
    public function type(): MessageType
    {
        return MessageType::Image;
    }

    public function fetch(): object
    {
        throw new \BadMethodCallException();
    }
}
