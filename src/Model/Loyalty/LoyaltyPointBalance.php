<?php
namespace Tustin\PlayStation\Model\Loyalty;

class LoyaltyPointBalance
{
    /**
     * Loyalty point balance data
     *
     * @var object
     */
    private $data;

    public function __construct(object $data)
    {
        $this->data = $data;
    }
}
