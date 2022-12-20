<?php

namespace Tustin\PlayStation\Model\Trophy;

use Tustin\PlayStation\Model;
use Tustin\PlayStation\Enum\TrophyType;
use Tustin\PlayStation\Interfaces\TrophyInterface;

class Trophy extends Model implements TrophyInterface
{
    public function __construct(private TrophyGroup $trophyGroup, private int $id)
    {
        parent::__construct($trophyGroup->getHttpClient());
    }

    /**
     * Gets the trophy title for this trophy.
     */
    public function title(): TrophyTitle
    {
        return $this->trophyTitle;
    }

    /**
     * Gets the trophy group for this trophy.
     */
    public function group(): TrophyGroup
    {
        return $this->trophyGroup;
    }

    /**
     * Gets the trophy name.
     */
    public function name(): string
    {
        return $this->pluck('trophyName');
    }

    /**
     * Gets the trophy id.
     */
    public function id(): int
    {
        return $this->id ??= $this->pluck('id');
    }

    /**
     * Gets the trophy details.
     */
    public function detail(): string
    {
        return $this->pluck('trophyDetail');
    }

    /**
     * Gets the trophy type. (platinum, bronze, silver, gold)
     */
    public function type(): TrophyType
    {
        return TrophyType::from($this->pluck('trophyType'));
    }

    /**
     * Check if the trophy is hidden.
     */
    public function hidden(): bool
    {
        return $this->pluck('trophyHidden');
    }

    /**
     * Gets the trophy icon URL.
     */
    public function iconUrl(): string
    {
        return $this->pluck('trophyIconUrl');
    }

    /**
     * Fetches the trophy data from the API.
     */
    public function fetch(): object
    {
        return $this->get('trophy/v1/npCommunicationIds/' . $this->trophyGroup->title()->npCommunicationId()  . '/trophies/' . $this->id(), [
            'npServiceName' => $this->trophyGroup->title()->serviceName()
        ]);
    }
}
