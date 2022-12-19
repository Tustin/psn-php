<?php

namespace Tustin\PlayStation\Model\Trophy;

use Tustin\PlayStation\Enum\TrophyLevelType;
use Tustin\PlayStation\Model;
use Tustin\PlayStation\Model\User;

class UserTrophySummary extends Model
{
    public function __construct(private User $user)
    {
        parent::__construct($user->getHttpClient());
    }

    /**
     * Gets the trophy level progress for the current level.
     */
    public function progress(): int
    {
        return $this->pluck('progress');
    }

    /**
     * Gets the trophy level tier.
     * 
     * @TODO: Maybe map out each tier and use an enum here?
     */
    public function tier(): int
    {
        return $this->pluck('tier');
    }

    /**
     * Gets the trophy level.
     */
    public function level(): int
    {
        return $this->pluck('trophyLevel');
    }

    /**
     * Gets the trophy level type.
     */
    public function trophyLevelType(): TrophyLevelType
    {
        $level = $this->level();

        return match(true) {
            $level <= 299 => TrophyLevelType::Bronze,
            $level <= 599 => TrophyLevelType::Silver,
            $level <= 998 => TrophyLevelType::Gold,
            $level >= 999 => TrophyLevelType::Platinum,
        };
    }

    /**
     * Gets the amount of earned bronze trophies.
     */
    public function bronze(): int
    {
        return $this->pluck('earnedTrophies.bronze');
    }

    /**
     * Gets the amount of earned silver trophies.
     */
    public function silver(): int
    {
        return $this->pluck('earnedTrophies.silver');
    }

    /**
     * Gets the amount of earned gold trophies.
     */
    public function gold(): int
    {
        return $this->pluck('earnedTrophies.gold');
    }

    /**
     * Gets the amount of earned platinum trophies.
     */
    public function platinum(): int
    {
        return $this->pluck('earnedTrophies.platinum');
    }

    /**
     * Fetches the user's trophy summary from the API.
     */
    public function fetch(): object
    {
        return $this->get('trophy/v1/users/' . $this->user->accountId() . '/trophySummary');
    }
}
