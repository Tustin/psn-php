<?php

namespace Tustin\PlayStation\Models\Messages;

use GdImage;
use Tustin\PlayStation\Enums\MessageType;

class ImageMessage extends Message
{
    /**
     * Gets the image.
     */
    public function image(): ?GdImage
    {
        // Not all image messages will have a resource. Could be due to legacy messages or something else.
        if (! $this->resourceId()) {
            return null;
        }

        // @TODO: catch and handle errors
        $image = $this->getRaw(
            "gamingLoungeGroups/v1/groups/{$this->messageThread()->id()}/resources/{$this->resourceId()}"
        );

        return imagecreatefromstring($image);
    }

    /**
     * Gets the image details.
     */
    public function imageDetails(): ?array
    {
        return $this->pluck('messageDetail.imageMessageDetail');
    }

    /**
     * Gets the resource id.
     */
    public function resourceId(): ?string
    {
        return $this->pluck('messageDetail.imageMessageDetail.resourceId');
    }

    /**
     * Gets the media id.
     */
    public function ugcId(): ?string
    {
        return $this->pluck('messageDetail.imageMessageDetail.ugcId');
    }

    /**
     * Gets the message type.
     */
    public function type(): MessageType
    {
        return MessageType::Image;
    }
}
