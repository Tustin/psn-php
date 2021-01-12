<?php
namespace Tustin\PlayStation\Model;

use Tustin\PlayStation\Enum\ConsoleType;
use Tustin\PlayStation\AbstractTrophyTitle;

class UserTrophyTitle extends AbstractTrophyTitle
{
    /**
     * Checks if this title has trophy groups.
     * 
     * These groups are typically DLC trophies.
     *
     * @return boolean
     */
    public function hasTrophyGroups() : bool
    {
        return $this->pluck('hasTrophyGroups');
    }

    /**
     * Gets the name of the title.
     *
     * @return string
     */
    public function name() : string
    {
        return $this->pluck('trophyTitleName');
    }

    /**
     * Gets the detail of the title.
     *
     * @return string
     */
    public function detail() : string
    {
        // PS5 titles doesn't seem to have the detail data.
        if ($this->pluck('npServiceName') == 'trophy2')
        {
            return '';
        }
        
        return $this->pluck('trophyTitleDetail');
    }

    /**
     * Gets the icon URL for the title.
     *
     * @return string
     */
    public function iconUrl() : string
    {
        return $this->pluck('trophyTitleIconUrl');
    }

    /**
     * Gets the platform this title is for.
     * 
     * @TODO: This might need to return an array??
     *
     * @return ConsoleType
     */
    public function platform() : ConsoleType
    {
        // @CheckMe
        return new ConsoleType($this->pluck('trophyTitlePlatform'));
    }

    /**
     * Checks if this title has trophies.
     *
     * @return boolean
     */
    public function hasTrophies() : bool
    {
        $value = $this->pluck('definedTrophies');
        
        return isset($value) && !empty($value);
    }

    /**
     * Checks if this title has a platinum trophy.
     *
     * @return boolean
     */
    public function hasPlatinum() : bool
    {
        return $this->pluck('definedTrophies.platinum') ?? false;
    }

    /**
     * Gets the total trophy count for this title.
     *
     * @return integer
     */
    public function trophyCount() : int
    {
        $count = ($this->bronzeTrophyCount() + $this->silverTrophyCount() + $this->goldTrophyCount());
        
        if ($this->hasPlatinum())
        {
            $count++;
        }

        return $count;
    }

    /**
     * Gets the amount of bronze trophies.
     *
     * @return integer
     */
    public function bronzeTrophyCount() : int
    {
        return $this->pluck('definedTrophies.bronze');
    }

    /**
     * Gets the amount of silver trophies.
     *
     * @return integer
     */
    public function silverTrophyCount() : int
    {
        return $this->pluck('definedTrophies.silver');
    }

    /**
     * Gets the amount of gold trophies.
     *
     * @return integer
     */
    public function goldTrophyCount() : int
    {
        return $this->pluck('definedTrophies.gold');
    }

    /**
     * Gets the NP communication ID (NPWR_) for this trophy title.
     *
     * @return string
     */
    public function npCommunicationId() : string
    {
        return $this->pluck('npCommunicationId');
    }

    /**
     * Gets the trophy list version number for this trophy title.
     *
     * @return string
     */
    public function trophySetVersion() : string
    {
        return $this->pluck('trophySetVersion');
    }
    
    /**
     * Gets the last updated date and time for the trophy title for this user.
     *
     * @return string
     */
    public function lastUpdatedDateTime() : string
    {
        return $this->pluck('lastUpdatedDateTime');
    }
    
    /**
     * Gets the amount of earned bronze trophies for this user.
     *
     * @return integer
     */
    public function earnedTrophiesBronzeCount() : int
    {
        return $this->pluck('earnedTrophies.bronze');
    }

    /**
     * Gets the amount of earned silver trophies for this user.
     *
     * @return integer
     */
    public function earnedTrophiesSilverCount() : int
    {
        return $this->pluck('earnedTrophies.silver');
    }

    /**
     * Gets the amount of earned gold trophies for this user.
     *
     * @return integer
     */
    public function earnedTrophiesGoldCount() : int
    {
        return $this->pluck('earnedTrophies.gold');
    }
    
    /**
     * Gets the amount of earned platinum trophies for this user.
     *
     * @return integer
     */
    public function earnedTrophiesPlatinumCount() : int
    {
        return $this->pluck('earnedTrophies.platinum');
    }
}
