<?php

namespace Tustin\PlayStation\Model\Message;

use Tustin\PlayStation\Model\Media;
use Tustin\PlayStation\Model\Message\AbstractMessage;

class ImageMessage extends AbstractMessage
{
    /**
     * Gets the image media.
     *
     * @return Media
     */
    public function image(): Media
    {
        return new Media($this->messageThread()->getHttpClient(), $this->pluck('messageDetail.imageMessageDetail.ugcId'));
    }

    /**
     * Gets the resource id.

     * @return string
     */
    public function resourceId(): string
    {
        return $this->pluck('messageDetail.imageMessageDetail.resourceId');
    }
}
