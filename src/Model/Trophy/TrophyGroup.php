<?php

namespace Tustin\PlayStation\Model\Trophy;

use Tustin\PlayStation\Model;
use Tustin\PlayStation\Enum\TrophyType;
use Tustin\PlayStation\Interfaces\TrophyGroupInterface;
use Tustin\PlayStation\Iterator\TrophyIterator;

class TrophyGroup extends Model implements TrophyGroupInterface
{
    public function __construct(private TrophyTitle $trophyTitle, private string $groupId)
    {
        parent::__construct($trophyTitle->getHttpClient());
    }
    
    /**
     * Gets the id for this trophy group.
     */
    public function id(): string
    {
        return $this->pluck('trophyGroupId');
    }

    /**
     * Gets the trophy title for this trophy group.
     */
    public function title(): TrophyTitle
    {
        return $this->trophyTitle;
    }

    /**
     * Gets all the trophies in the trophy group.
     */
    public function trophies(): \Iterator
    {
        return new TrophyIterator($this);
    }

    /**
     * Gets the trophy group name.
     */
    public function name(): string
    {
        return $this->pluck('trophyGroupName');
    }

    /**
     * Gets the trophy group detail.
     */
    public function detail(): string
    {
        return $this->pluck('trophyGroupDetail') ?? '';
    }

    /**
     * Gets the trophy group icon URL.
     */
    public function iconUrl(): string
    {
        return $this->pluck('trophyGroupIconUrl');
    }

    /**
     * Gets the defined trophies for this trophy group.
     */
    public function definedTrophies(): array
    {
        return $this->pluck('definedTrophies');
    }

    /**
     * Gets the bronze trophy count.
     */
    public function bronze(): int
    {
        return $this->pluck('definedTrophies.bronze');
    }

    /**
     * Gets the silver trophy count.
     */
    public function silver(): int
    {
        return $this->pluck('definedTrophies.silver');
    }

    /**
     * Gets the gold trophy count.
     */
    public function gold(): int
    {
        return $this->pluck('definedTrophies.gold');
    }

    /**
     * Gets whether this trophy group has a platinum or not.
     */
    public function hasPlatinum(): bool
    {
        return $this->pluck('definedTrophies.platinum') == 1;
    }

    /**
     * Gets the trophy count for a specificed trophy type.
     */
    public function trophyCount(TrophyType $trophyType): int
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
     * Gets the amount of trophies in the trophy group.
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
            'trophy/v1/npCommunicationIds/' . $this->title()->npCommunicationId()  . '/trophyGroups',
            [
                'npServiceName' => $this->title()->serviceName()
            ]
        );
    }
}
