<?php
namespace Tustin\PlayStation\Model\Loyalty;

use Carbon\Carbon;

class LoyaltyStatusLevel
{
    /**
     * Loyalty status level data
     *
     * @var object
     */
    private $data;

    public function __construct(object $data)
    {
        $this->data = $data;
    }

    /**
     * Gets the current reward tier name.
     *
     * @return string
     */
    public function currentStatusLevel(): string
    {
        return $this->data->currentStatusLevel;
    }

    /**
     * Gets the expiry date.
     * 
     * @todo Is this for the current tier or points
     *
     * @return \DateTime
     */
    public function expiryDate(): ?\DateTime
    {
        // if ($this->data->expiryDate === null) {
        //     return null;
        // }

        return Carbon::parse($this->data->expiryDate);
    }

    /**
     * Gets the progress towards the next reward tier.
     *
     * @return integer
     */
    public function nextStatusProgress(): int
    {
        return (int)$this->data->statusLevelNumber;
    }

    /**
     * Gets the current reward tier.
     *
     * @return integer
     */
    public function statusLevelNumber(): int
    {
        return (int)$this->data->statusLevelNumber;
    }

    /**
     * Gets the amount of purchases made towards the next reward tier.
     *
     * @return integer
     */
    public function totalPurchaseEarned(): int
    {
        return (int)$this->data->totalPurchaseEarned;
    }

    /**
     * Gets the total amount of purchases required for the next reward tier.
     *
     * @return integer
     */
    public function totalPurchaseNeeded(): int
    {
        return (int)$this->data->totalPurchaseNeeded;
    }

    /**
     * Gets the total amount of trophies earned towards the next reward tier.
     *
     * @return integer
     */
    public function totalTrophiesEarned(): int
    {
        return (int)$this->data->totalTrophiesEarned;
    }

    /**
     * Gets the total amount of trophies needed for the next reward tier.
     *
     * @return integer
     */
    public function totalTrophiesNeeded(): int
    {
        return (int)$this->data->totalTrophiesNeeded;
    }
}