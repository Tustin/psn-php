<?php
namespace Tustin\PlayStation\Model\Loyalty;

class LoyaltyEnrollStatus
{
    /**
     * Loyalty enrollment status data
     *
     * @var object
     */
    private $data;

    public function __construct(object $data)
    {
        $this->data = $data;
    }
}
