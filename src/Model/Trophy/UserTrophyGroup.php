<?php

namespace Tustin\PlayStation\Model\Trophy;

use Carbon\Carbon;
use Tustin\PlayStation\Model\User;
use Tustin\PlayStation\Traits\HasUser;
use Tustin\PlayStation\Enum\TrophyType;
use Tustin\PlayStation\Model\Trophy\UserTrophyTitle;
use Tustin\PlayStation\Interfaces\TrophyGroupInterface;
use Tustin\PlayStation\Iterator\UserTrophyIterator;

class UserTrophyGroup extends TrophyGroup implements TrophyGroupInterface
{
    use HasUser;

    public function __construct(private UserTrophyTitle $trophyTitle, private User $user, private string $groupId)
    {
        parent::__construct($trophyTitle, $groupId);
    }

    /**
     * Gets the user's trophy title for this trophy group.
     */
    public function title(): UserTrophyTitle
    {
        return $this->trophyTitle;
    }

    /**
     * Gets all the trophies in the user's trophy group.
     */
    public function trophies(): \Iterator
    {
        return new UserTrophyIterator($this);
    }

    /**
     * Gets the earned trophies for this trophy group.
     */
    public function earnedTrophies(): array
    {
        return $this->pluck('earnedTrophies');
    }

    /**
     * Gets the amount of bronze trophies earned in this trophy group.
     */
    public function bronze(): int
    {
        return $this->pluck('earnedTrophies.bronze');
    }

    /**
     * Gets the amount of silver trophies earned in this trophy group.
     */
    public function silver(): int
    {
        return $this->pluck('earnedTrophies.silver');
    }

    /**
     * Gets the amount of gold trophies earned in this trophy group.
     */
    public function gold(): int
    {
        return $this->pluck('earnedTrophies.gold');
    }

    /**
     * Gets whether the user has earned a platinum trophy for this trophy group.
     */
    public function hasPlatinum(): bool
    {
        return $this->pluck('earnedTrophies.platinum') == 1;
    }

    /**
     * Gets the completion progress for this trophy group.
     */
    public function progress(): int
    {
        return $this->pluck('progress');
    }

    /**
     * Gets the last updated date time for this trophy group.
     */
    public function lastUpdated(): \DateTime
    {
        return Carbon::parse($this->pluck('lastUpdatedDateTime'));
    }

    /**
     * Gets the earned trophy count for a specificed trophy type.
     */
    public function earnedTrophyCount(TrophyType $trophyType): int
    {
        switch ($trophyType) {
            case TrophyType::Bronze:
                return $this->bronze();
            case TrophyType::Silver:
                return $this->silver();
            case TrophyType::Gold:
                return $this->gold();
            case TrophyType::Platinum:
                return (int)$this->hasPlatinum();
            default:
                throw new \InvalidArgumentException("Trophy type [$trophyType] does not contain a count method.");
        }
    }

    /**
     * Gets the amount of trophies earned in this trophy group.
     */
    public function totalTrophyCount(): int
    {
        $count = $this->bronze() + $this->silver() + $this->gold();

        return $this->hasPlatinum() ? ++$count : $count;
    }

    /**
     * Fetches the trophy group information from the API.
     */
    public function fetch(): object
    {
        return $this->get(
            'trophy/v1/users/' . $this->user->accountId() . '/npCommunicationIds/' . $this->title()->npCommunicationId() . '/trophyGroups',
            [
                'npServiceName' => $this->title()->serviceName()
            ]
        );
    }
}
