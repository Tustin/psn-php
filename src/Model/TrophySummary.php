<?php

namespace Tustin\PlayStation\Model;

use Tustin\PlayStation\Api\Api;
use Tustin\PlayStation\Model\User;
use Tustin\PlayStation\Traits\Model;
use Tustin\PlayStation\Interfaces\Fetchable;

class TrophySummary extends Api implements Fetchable
{
    use Model;

    /**
     * The user for the trophy summary.
     *
     * @var User
     */
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
        parent::__construct($user->getHttpClient());
    }

    /**
     * Gets the trophy level progress for the current level.
     *
     * @return integer
     */
    public function progress() : int
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
    public function tier() : int
    {
        return $this->pluck('tier');
    }

    /**
     * Gets the trophy level.
     *
     * @return integer
     */
    public function level() : int
    {
        return $this->pluck('level');
    }

    /**
     * Gets the amount of bronze trophies.
     *
     * @return integer
     */
    public function bronze() : int
    {
        return $this->pluck('earnedTrophies.bronze');
    }

    /**
     * Gets the amount of silver trophies.
     *
     * @return integer
     */
    public function silver() : int
    {
        return $this->pluck('earnedTrophies.silver');
    }

    /**
     * Gets the amount of gold trophies.
     *
     * @return integer
     */
    public function gold() : int
    {
        return $this->pluck('earnedTrophies.gold');
    }

    
    /**
     * Gets the amount of platinum trophies.
     *
     * @return integer
     */
    public function platinum() : int
    {
        return $this->pluck('earnedTrophies.platinum');
    }

    /**
     * Fetches the trophy summary from the API.
     *
     * @return object
     */
    public function fetch() : object
    {
        return $this->get('trophy/v1/users/' . $this->user->accountId() . '/trophySummary');
    }
}