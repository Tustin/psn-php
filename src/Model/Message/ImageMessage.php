<?php

namespace Tustin\PlayStation\Model\Message;

use Tustin\PlayStation\Model\Media;
use Tustin\PlayStation\Enum\MessageType;
use Tustin\PlayStation\Model\Message\AbstractMessage;

class ImageMessage extends AbstractMessage
{
    /**
     * Gets the image media.
     */
    public function image(): Media
    {
        return new Media($this->messageThread()->getHttpClient(), $this->pluck('messageDetail.imageMessageDetail.ugcId'));
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
