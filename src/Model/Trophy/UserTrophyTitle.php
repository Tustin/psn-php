<?php
namespace Tustin\PlayStation\Model\Trophy;

use Tustin\PlayStation\Enum\ConsoleType;

class UserTrophyTitle extends AbstractTrophyTitle
{
    /**
     * Checks if this title has trophy groups.
     * 
     * These groups are typically DLC trophies.
     *
     * @return boolean
     */
    public function hasTrophyGroups(): bool
    {
        return $this->pluck('hasTrophyGroups');
    }

    /**
     * Gets the name of the title.
     *
     * @return string
     */
    public function name(): string
    {
        return $this->pluck('trophyTitleName');
    }

    /**
     * Gets the detail of the title.
     *
     * @return string
     */
    public function detail(): string
    {
        // PS5 titles doesn't seem to have the detail data.
        if ($this->serviceName() == 'trophy2')
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
    public function iconUrl(): string
    {
        return $this->pluck('trophyTitleIconUrl');
    }

    /**
     * Gets the platform(s) this title is for.
     *
     * @return array
     */
    public function platform(): array
    {
        $platforms = [];

        foreach (explode(",", $this->pluck('trophyTitlePlatform')) as $platform)
        {
            $platforms[] = new ConsoleType($platform);
        }
        
        return $platforms;
    }

    /**
     * Checks if this title has trophies.
     *
     * @return boolean
     */
    public function hasTrophies(): bool
    {
        $value = $this->pluck('definedTrophies');
        
        return isset($value) && !empty($value);
    }

    /**
     * Checks if this title has a platinum trophy.
     *
     * @return boolean
     */
    public function hasPlatinum(): bool
    {
        return $this->pluck('definedTrophies.platinum') ?? false;
    }

    /**
     * Gets the total trophy count for this title.
     *
     * @return integer
     */
    public function trophyCount(): int
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
    public function bronzeTrophyCount(): int
    {
        return $this->pluck('definedTrophies.bronze');
    }

    /**
     * Gets the amount of silver trophies.
     *
     * @return integer
     */
    public function silverTrophyCount(): int
    {
        return $this->pluck('definedTrophies.silver');
    }

    /**
     * Gets the amount of gold trophies.
     *
     * @return integer
     */
    public function goldTrophyCount(): int
    {
        return $this->pluck('definedTrophies.gold');
    }

    /**
     * Gets the NP communication ID (NPWR_) for this trophy title.
     *
     * @return string
     */
    public function npCommunicationId(): string
    {
        return $this->pluck('npCommunicationId');
    }

    /**
     * Gets the trophy list version number for this trophy title.
     *
     * @return string
     */
    public function trophySetVersion(): string
    {
        return $this->pluck('trophySetVersion');
    }
    
    /**
     * Gets the last updated date and time for the trophy title for this user.
     *
     * @return string
     */
    public function lastUpdatedDateTime(): string
    {
        return $this->pluck('lastUpdatedDateTime');
    }
    
    /**
     * Gets the amount of earned bronze trophies for this user.
     *
     * @return integer
     */
    public function earnedTrophiesBronzeCount(): int
    {
        return $this->pluck('earnedTrophies.bronze');
    }

    /**
     * Gets the amount of earned silver trophies for this user.
     *
     * @return integer
     */
    public function earnedTrophiesSilverCount(): int
    {
        return $this->pluck('earnedTrophies.silver');
    }

    /**
     * Gets the amount of earned gold trophies for this user.
     *
     * @return integer
     */
    public function earnedTrophiesGoldCount(): int
    {
        return $this->pluck('earnedTrophies.gold');
    }
    
    /**
     * Gets the amount of earned platinum trophies for this user.
     *
     * @return integer
     */
    public function earnedTrophiesPlatinumCount(): int
    {
        return $this->pluck('earnedTrophies.platinum');
    }
    
    /**
     * Gets the trophy title progress percent for this user.
     *
     * @return integer
     */
    public function progress(): int
    {
        return $this->pluck('progress');
	}
	
    /**
     * Gets the trophy service name for this trophy.
     *
     * @return string
     */
	public function serviceName(): string
	{
		return $this->serviceName ??= $this->pluck('npServiceName');
	}

    // @TODO: Implement
    public function fetch(): object
    {
        throw new \BadMethodCallException();
    }
}
