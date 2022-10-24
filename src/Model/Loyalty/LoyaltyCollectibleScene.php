<?php
namespace Tustin\PlayStation\Model\Loyalty;

class LoyaltyCollectibleScene
{
    /**
     * Loyalty collectible data
     *
     * @var object
     */
    private $data;

    public function __construct(object $data)
    {
        $this->data = $data;
    }

    /**
     * Gets the media assets for the item.
     *
     * @return SceneMedia[]
     */
    public function assets(): array
    {
        $assets = [];

        foreach ($this->data->assets as $asset)
        {
            $assets[] = new SceneMedia($asset);
        }

        return $assets;
    }

    /**
     * Gets the GUID for the scene.
     *
     * @return string
     */
    public function id(): string
    {
        return $this->data->id;
    }
}
