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

    /**
     * Gets the user's total points balance in string format.
     *
     * @return string
     */
    public function displayTotalPointsBalance(): string
    {
        return $this->displayTotalPointsBalance;
    }

    /**
     * Gets the user's total amount of campaigns completed.
     *
     * @return integer
     */
    public function totalCampaignsCompleted(): int
    {
        return (int)$this->totalCampaignsCompleted;
    }
    
    /**
     * Gets the user's total points balance.
     *
     * @return integer
     */
    public function totalPointsBalance(): int
    {
        return (int)$this->totalPointsBalance;
    }
}
