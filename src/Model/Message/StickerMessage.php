<?php

namespace Tustin\PlayStation\Model\Message;

use Tustin\PlayStation\Enum\MessageType;
use Tustin\PlayStation\Model\Message\AbstractMessage;

class StickerMessage extends AbstractMessage
{
    public function url(): string
    {
        return $this->pluck('messageDetail.stickerMessageDetail.imageUrl');
    }

    public function manifestUrl(): string
    {
        return $this->pluck('messageDetail.stickerMessageDetail.manifestFileUrl');
    }

    public function packageId(): string
    {
        return $this->pluck('messageDetail.stickerMessageDetail.packageId');
    }

    public function number(): string
    {
        return $this->pluck('messageDetail.stickerMessageDetail.number');
    }

    public function type(): MessageType
    {
        return MessageType::Sticker;
    }

    public function fetch(): object
    {
        throw new \BadMethodCallException();
    }
}
