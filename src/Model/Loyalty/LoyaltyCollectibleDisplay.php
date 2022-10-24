<?php
namespace Tustin\PlayStation\Model\Loyalty;

class LoyaltyCollectibleDisplay
{
    /**
     * Loyalty collectible display data
     *
     * @var object
     */
    private $data;

    public function __construct(object $data)
    {
        $this->data = $data;
    }

    /**
     * Gets the collectibles in the display.
     *
     * @return LoyaltyCollectibleDisplayItem[]
     */
    public function collectibles(): array
    {
        $items = [];

        foreach ($this->data->collectibles as $collectible)
        {
            $items[] = new LoyaltyCollectibleDisplayItem($collectible);
        }

        return $items;
    }

    /**
     * Checks if the user's display is empty.
     *
     * @return boolean
     */
    public function isEmpty(): bool
    {
        return $this->data->isEmpty;
    }

    /**
     * Checks if the user is displaying 12 items.
     *
     * @return boolean
     */
    public function isFull(): bool
    {
        return $this->data->isFull;
    }

    /**
     * Gets the user's selected scene.
     *
     * @return LoyaltyCollectibleScene
     */
    public function selectedScene(): LoyaltyCollectibleScene
    {
        return new LoyaltyCollectibleScene($this->data->selectedScene);
    }
}
