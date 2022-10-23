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
}
