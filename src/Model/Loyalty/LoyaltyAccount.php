<?php
namespace Tustin\PlayStation\Model\Loyalty;

use Tustin\PlayStation\Model\Loyalty\LoyaltyPointBalance;
use Tustin\PlayStation\Exception\AuthenticatingAccountOnlyException;

class LoyaltyAccount
{
    /**
     * Loyalty account data
     *
     * @var object
     */
    private $data;

    public function __construct(object $data)
    {
        $this->data = $data->loyaltyAccountRetrieve;
    }

    /**
     * Gets the collectibles in the display case.
     *
     * @return LoyaltyCollectibleScene
     */
    public function collectibleScene(): LoyaltyCollectibleScene
    {
        return new LoyaltyCollectibleScene($this->data->collectibleScene);
    }

    /**
     * Gets the first 3 collectibles in the display case.
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
     * Gets the user's enrollment status.
     *
     * @return LoyaltyEnrollStatus
     */
    public function enrollStatus(): LoyaltyEnrollStatus
    {
       return new LoyaltyEnrollStatus($this->data->enrollStatus);
    }

    /**
     * Gets the user's loyalty point balace.
     * 
     * This function will only work for the authenticated user's account.
     *
     * @return LoyaltyPointBalance
     */
    public function pointsBalance(): LoyaltyPointBalance
    {
        if ($this->data->pointsBalance === null) {
            throw new AuthenticatingAccountOnlyException('Only the authenticated account can retrieve the points balance.');
        }

        return new LoyaltyPointBalance($this->data->pointsBalance);
    }

    /**
     * Status of the user's TOS loyalty acceptance.
     * 
     * This function will only work for the authenticated user's account.
     *
     * @return boolean
     */
    public function requiresTosAcceptance(): bool
    {
        if ($this->data->requiresTosAcceptance === null) {
            throw new AuthenticatingAccountOnlyException('Only the authenticated account can retrieve the TOS acceptance status.');
        }

        return (bool)$this->data->requiresTosAcceptance;
    }

    /**
     * Gets the user's loyalty status level.
     *
     * @return LoyaltyStatusLevel
     */
    public function statusLevel(): LoyaltyStatusLevel
    {
        return new LoyaltyStatusLevel($this->data->statusLevel);
    }

    /**
     * Gets the raw response data for the loyalty account.
     *
     * @return object
     */
    public function raw(): object
    {
        return $this->data;
    }
}
