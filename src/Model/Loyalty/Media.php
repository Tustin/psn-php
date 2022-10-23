<?php
namespace Tustin\PlayStation\Model\Loyalty;

class Media
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

    public function role(): string
    {
        return $this->data->role;
    }

    public function url(): string
    {
        return $this->data->url;
    }
}
