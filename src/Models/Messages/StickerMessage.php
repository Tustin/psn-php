<?php

namespace Tustin\PlayStation\Models\Messages;

use Tustin\PlayStation\Enums\MessageType;

class StickerMessage extends Message
{
    /**
     * Gets the sticker url.
     */
    public function url(): string
    {
        return $this->pluck('messageDetail.stickerMessageDetail.imageUrl');
    }

    /**
     * Gets the sticker manifest url.
     */
    public function manifestUrl(): string
    {
        return $this->pluck('messageDetail.stickerMessageDetail.manifestFileUrl');
    }

    /**
     * Gets the sticker package id.
     */
    public function packageId(): string
    {
        return $this->pluck('messageDetail.stickerMessageDetail.packageId');
    }

    /**
     * Gets the sticker number.
     */
    public function number(): string
    {
        return $this->pluck('messageDetail.stickerMessageDetail.number');
    }

    /**
     * Gets the message type
     */
    public function type(): MessageType
    {
        return MessageType::Sticker;
    }

    public function fetch(): object
    {
        throw new \BadMethodCallException();
    }
}
