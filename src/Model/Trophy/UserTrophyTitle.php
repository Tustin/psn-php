<?php

namespace Tustin\PlayStation\Model\Trophy;

use GuzzleHttp\Client;
use Tustin\PlayStation\Traits\HasUser;

class UserTrophyTitle extends TrophyTitle
{
    use HasUser;

    public static function fromObject(Client $client, object $data): self
    {
        return (new static($client, $data->npCommunicationId, $data->npServiceName))->withCache($data);
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
    public function earnedBronzeTrophiesCount(): int
    {
        return $this->pluck('earnedTrophies.bronze');
    }

    /**
     * Gets the amount of earned silver trophies for this user.
     */
    public function earnedSilverTrophiesCount(): int
    {
        return $this->pluck('earnedTrophies.silver');
    }

    /**
     * Gets the amount of earned gold trophies for this user.
     */
    public function earnedGoldTrophiesCount(): int
    {
        return $this->pluck('earnedTrophies.gold');
    }

    /**
     * Checks if user has earned the platinum trophy for this title.
     */
    public function earnedPlatinumTrophy(): bool
    {
        return $this->pluck('earnedTrophies.platinum') == 1;
    }

    /**
     * Gets the trophy title progress percent for this user.
     */
    public function progress(): int
    {
        return $this->pluck('progress');
    }

    /**
     * Gets the trophy title hidden status for this user.
     */
    public function hidden(): bool
    {
        return $this->pluck('hiddenFlag');
    }

    /**
     * Gets the user trophy title information from the API.
     */
    public function fetch(): object
    {
        return $this->get(
            'trophy/v1/users/' . $this->user()->accountId() . '/npCommunicationIds/' . $this->npCommunicationId() . '/trophyGroups',
            [
                'npServiceName' => $this->serviceName()
            ]
        );
    }
}
