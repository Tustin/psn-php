<?php
namespace Tustin\PlayStation\Model\Loyalty;

class LoyaltyCollectibleDisplayItem
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
     * @return CollectibleMedia[]
     */
    public function assets(): array
    {
        $assets = [];

        foreach ($this->data->assets as $asset)
        {
            $assets[] = new CollectibleMedia($asset);
        }

        return $assets;
    }

    /**
     * Gets the GUID for the current item.
     * 
     * If there is no item, the value will return "empty-slot-x", with x being the item position in the display.
     *
     * @return string
     */
    public function id(): string
    {
        return $this->data->id;
    }

    /**
     * Checks if there is no display item.
     *
     * @return boolean
     */
    public function isEmpty(): bool
    {
        return $this->data->isEmpty;
    }
}
