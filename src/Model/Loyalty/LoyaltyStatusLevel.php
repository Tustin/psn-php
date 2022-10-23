<?php
namespace Tustin\PlayStation\Model\Loyalty;

class LoyaltyStatusLevel
{
    /**
     * Loyalty stauts level data
     *
     * @var object
     */
    private $data;

    public function __construct(object $data)
    {
        $this->data = $data;
    }
}
