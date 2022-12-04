<?php

namespace Tustin\PlayStation\Model\Trophy;

use Tustin\PlayStation\Model;
use Tustin\PlayStation\Model\User;

class TrophySummary extends Model
{
    public function __construct(private User $user)
    {
        $this->user = $user;

        parent::__construct($user->getHttpClient());
    }

    /**
     * Gets the trophy level progress for the current level.
     *
     * @return integer
     */
    public function progress(): int
    {
        return $this->pluck('progress');
    }

    /**
     * Gets the trophy level tier.
     * 
     * @TODO: Maybe map out each tier and use an enum here?
     *
     * @return integer
     */
    public function tier(): int
    {
        return $this->pluck('tier');
    }

    /**
     * Gets the trophy level.
     *
     * @return integer
     */
    public function level(): int
    {
        return $this->pluck('trophyLevel');
    }

    /**
     * Gets the amount of bronze trophies.
     *
     * @return integer
     */
    public function bronze(): int
    {
        return $this->pluck('earnedTrophies.bronze');
    }

    /**
     * Gets the amount of silver trophies.
     *
     * @return integer
     */
    public function silver(): int
    {
        return $this->pluck('earnedTrophies.silver');
    }

    /**
     * Gets the amount of gold trophies.
     *
     * @return integer
     */
    public function gold(): int
    {
        return $this->pluck('earnedTrophies.gold');
    }

    
    /**
     * Gets the amount of platinum trophies.
     *
     * @return integer
     */
    public function platinum(): int
    {
        return $this->pluck('earnedTrophies.platinum');
    }

    /**
     * Fetches the trophy summary from the API.
     *
     * @return object
     */
    public function fetch(): object
    {
        return $this->get('trophy/v1/users/' . $this->user->accountId() . '/trophySummary');
    }
}
