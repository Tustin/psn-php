<?php

namespace Tustin\PlayStation\Model\Trophy;

use Tustin\PlayStation\Model;
use Tustin\PlayStation\Enum\TrophyType;
use Tustin\PlayStation\Factory\TrophyFactory;

class TrophyGroup extends Model
{
    public function __construct(
        private AbstractTrophyTitle $trophyTitle,
        private string $groupId,
        private string $groupName = '',
        private string $groupIconUrl = '',
        private string $groupDetail = ''
    ) {
        parent::__construct($trophyTitle->getHttpClient());
    }

    /**
     * Creates a new trophy group from existing data.
     */
    public static function fromObject(AbstractTrophyTitle $trophyTitle, object $data): self
    {
        $instance = new static($trophyTitle, $data->trophyGroupId, $data->trophyGroupName, $data->trophyGroupIconUrl, $data->trophyGroupDetail);
        $instance->setCache($data);

        return $instance;
    }

    /**
     * Gets the trophy title for this trophy group.
     */
    public function title(): AbstractTrophyTitle
    {
        return $this->trophyTitle;
    }

    /**
     * Gets all the trophies in the trophy group.
     */
    public function trophies(): TrophyFactory
    {
        return new TrophyFactory($this);
    }

    /**
     * Gets the trophy group name.
     */
    public function name(): string
    {
        return $this->groupName;
    }

    /**
     * Gets the trophy group detail.
     */
    public function detail(): string
    {
        return $this->groupDetail ?? '';
    }

    /**
     * Gets the trophy group ID.
     */
    public function id(): string
    {
        return $this->groupId;
    }

    /**
     * Gets the trophy group icon URL.
     */
    public function iconUrl(): string
    {
        return $this->groupIconUrl;
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
        if ($this->title() instanceof UserTrophyTitle) {
            return $this->get(
                'trophy/v1/users/' . $this->title()->getFactory()->getUser()->accountId() . '/npCommunicationIds/' . $this->title()->npCommunicationId() . '/trophyGroups',
                [
                    'npServiceName' => $this->title()->serviceName()
                ]
            );
        } else {
            return $this->get(
                'trophy/v1/npCommunicationIds/' . $this->title()->npCommunicationId()  . '/trophyGroups',
                [
                    'npServiceName' => $this->title()->serviceName()
                ]
            );
        }
    }
}
