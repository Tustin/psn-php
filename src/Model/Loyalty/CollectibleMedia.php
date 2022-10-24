<?php
namespace Tustin\PlayStation\Model\Loyalty;

class CollectibleMedia
{
    /**
     * Media data
     *
     * @var object
     */
    private $media;

    public function __construct(object $media)
    {
        $this->media = $media;
    }

    /**
     * Gets the media type.
     * 
     * IMAGE, VIDEO
     * 
     * @return string
     */
    public function type(): string
    {
        return $this->media->type;
    }

    /**
     * Gets the URL for the media.
     *
     * @return string
     */
    public function url(): string
    {
        return $this->media->url;
    }
}
