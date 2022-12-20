<?php

namespace Tustin\PlayStation\Model\Trophy;

use Carbon\Carbon;
use Tustin\PlayStation\Model\Trophy\Trophy;
use Tustin\PlayStation\Interfaces\TrophyInterface;

class UserTrophy extends Trophy implements TrophyInterface
{
    public function __construct(private UserTrophyGroup $trophyGroup, private int $id)
    {
        parent::__construct($trophyGroup, $id);
    }

    public function title(): UserTrophyTitle
    {
        return $this->trophyTitle;
    }

    public function group(): UserTrophyGroup
    {
        return $this->trophyGroup;
    }

    /**
     * Get the trophy earned rate.
     */
    public function earnedRate(): float
    {
        return $this->pluck('trophyEarnedRate');
    }

    /**
     * Gets the trophy progress target value, if any.
     */
    public function progressTargetValue(): string
    {
        return $this->pluck('trophyProgressTargetValue') ?? '';
    }

    /**
     * Gets the trophy reward name, if any.
     * 
     * Examples: "Emote", "Profile Avatar", "Profile Banner"
     */
    public function rewardName(): string
    {
        return $this->pluck('trophyRewardName') ?? '';
    }

    /**
     * Gets the trophy reward image url, if any.
     */
    public function rewardImageUrl(): string
    {
        return $this->pluck('trophyRewardImageUrl') ?? '';
    }

    /**
     * Check if the user has earned this trophy.
     */
    public function earned(): bool
    {
        return $this->pluck('earned');
    }

    /**
     * Get the date and time the user earned this trophy.
     */
    public function earnedDateTime(): ?\DateTime
    {
        if (!$this->earned()) {
            return null;
        }

        return Carbon::parse($this->pluck('earnedDateTime'));
    }

    /**
     * Get the progress count for the user on this trophy, if any.
     */
    public function progress(): string
    {
        return $this->pluck('progress') ?? '';
    }

    /**
     * Get the progress percentage for the user on this trophy, if any.
     */
    public function progressRate(): string
    {
        return $this->pluck('progressRate') ?? '';
    }

    /**
     * Get the date and time when a progress was made for the user on this trophy, if any.
     */
    public function progressedDateTime(): string
    {
        return $this->pluck('progressedDateTime') ?? '';
    }

    /**
     * Fetches the trophy data from the API.
     */
    public function fetch(): object
    {
        return $this->get('trophy/v1/users/' . $this->trophyGroup->user()->accountId() . '/npCommunicationIds/' . $this->trophyGroup->title()->npCommunicationId()  . '/trophies/' . $this->id(), [
            'npServiceName' => $this->trophyGroup->title()->serviceName()
        ]);
    }
}
