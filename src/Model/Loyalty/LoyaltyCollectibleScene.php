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
}
