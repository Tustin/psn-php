<?php
namespace Tustin\PlayStation\Model\Loyalty;

class SceneMedia
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
     * Gets the role for the media.
     * 
     * BACKGROUND, TITLE, PREVIEW
     *
     * @return string
     */
    public function role(): string
    {
        return $this->media->role;
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
