<?php

namespace Tustin\PlayStation\Models\Trophy;

use Tustin\PlayStation\Enums\TrophyServiceName;
use Tustin\PlayStation\Enums\TrophyTitlePlatform;

class UserTrophyTitle extends AbstractTrophyTitle
{
    /**
     * Checks if this title has trophy groups.
     * 
     * These groups are typically DLC trophies.
     */
    public function hasTrophyGroups(): bool
    {
        return $this->pluck('hasTrophyGroups');
    }

    /**
     * Gets the name of the title.
     */
    public function name(): string
    {
        return $this->pluck('trophyTitleName');
    }

    /**
     * Gets the detail of the title.
     * 
     * PS5 titles do not have any detail, so they will always return null.
     */
    public function detail(): ?string
    {
        // PS5 titles don't seem to have the detail data.
        if ($this->serviceName() == TrophyServiceName::Trophy2) {
            return null;
        }

        return $this->pluck('trophyTitleDetail');
    }

    /**
     * Gets the icon URL for the title.
     */
    public function iconUrl(): string
    {
        return $this->pluck('trophyTitleIconUrl');
    }

    /**
     * Gets the platform(s) this title is for.
     *
     * @return array<TrophyTitlePlatform>
     */
    public function platform(): array
    {
        $platforms = [];

        foreach (explode(",", $this->pluck('trophyTitlePlatform')) as $platform) {
            $platforms[] = TrophyTitlePlatform::tryFrom($platform);
        }

        return $platforms;
    }

    /**
     * Checks if this title has trophies.
     */
    public function hasTrophies(): bool
    {
        $value = $this->pluck('definedTrophies');

        return isset($value) && !empty($value);
    }

    /**
     * Checks if this title has a platinum trophy.
     */
    public function hasPlatinum(): bool
    {
        return $this->pluck('definedTrophies.platinum') ?? false;
    }

    /**
     * Gets the total trophy count for this title.
     */
    public function trophyCount(): int
    {
        $count = ($this->bronzeTrophyCount() + $this->silverTrophyCount() + $this->goldTrophyCount());

        if ($this->hasPlatinum()) {
            $count++;
        }

        return $count;
    }

    /**
     * Gets the amount of bronze trophies.
     */
    public function bronzeTrophyCount(): int
    {
        return $this->pluck('definedTrophies.bronze');
    }

    /**
     * Gets the amount of silver trophies.
     */
    public function silverTrophyCount(): int
    {
        return $this->pluck('definedTrophies.silver');
    }

    /**
     * Gets the amount of gold trophies.
     */
    public function goldTrophyCount(): int
    {
        return $this->pluck('definedTrophies.gold');
    }

    /**
     * Gets the NP communication ID (NPWR_) for this trophy title.
     */
    public function npCommunicationId(): string
    {
        return $this->pluck('npCommunicationId');
    }

    /**
     * Gets the trophy list version number for this trophy title.
     */
    public function trophySetVersion(): string
    {
        return $this->pluck('trophySetVersion');
    }

    /**
     * Gets the last updated date and time for the trophy title for this user.
     */
    public function lastUpdatedDateTime(): string
    {
        return $this->pluck('lastUpdatedDateTime');
    }

    /**
     * Gets the amount of earned bronze trophies for this user.
     */
    public function earnedTrophiesBronzeCount(): int
    {
        return $this->pluck('earnedTrophies.bronze');
    }

    /**
     * Gets the amount of earned silver trophies for this user.
     */
    public function earnedTrophiesSilverCount(): int
    {
        return $this->pluck('earnedTrophies.silver');
    }

    /**
     * Gets the amount of earned gold trophies for this user.
     */
    public function earnedTrophiesGoldCount(): int
    {
        return $this->pluck('earnedTrophies.gold');
    }

    /**
     * Gets the amount of earned platinum trophies for this user.
     */
    public function earnedTrophiesPlatinumCount(): int
    {
        return $this->pluck('earnedTrophies.platinum');
    }

    /**
     * Gets the amount of total earned trophies for this user.
     */
    public function earnedTrophiesCount(): int
    {
        return $this->earnedTrophiesBronzeCount() + $this->earnedTrophiesSilverCount() + $this->earnedTrophiesGoldCount() + $this->earnedTrophiesPlatinumCount();
    }

    /**
     * Gets the trophy title progress percent for this user.
     */
    public function progress(): int
    {
        return $this->pluck('progress');
    }

    /**
     * Gets the trophy service name for this trophy.
     */
    public function serviceName(): TrophyServiceName
    {
        return $this->serviceName ??= TrophyServiceName::tryFrom($this->pluck('npServiceName'));
    }

    /**
     * @throws \BadMethodCallException
     */
    public function fetch(): object
    {
        throw new \BadMethodCallException(
            'This method is not supported for user trophy titles.'
        );
    }
}
