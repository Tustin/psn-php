<?php

namespace Tustin\PlayStation\Model\Trophy;

use GuzzleHttp\Client;
use Tustin\PlayStation\Enum\ConsoleType;

class TrophyTitle extends AbstractTrophyTitle
{
    public function __construct(Client $client, string $npCommunicationId, string $serviceName = 'trophy')
    {
        parent::__construct($client, $npCommunicationId);

        $this->setServiceName($serviceName);
    }

    public static function fromObject(Client $client, object $data): self
    {
        $title = new static($client, $data->npCommunicationId, $data->npServiceName);
        $title->setCache($data);

        return $title;
    }

    /**
     * Get the trophy set version for this trophy title.
     */
    public function version(): string
    {
        return $this->pluck('version');
    }

    /**
     * Gets the trophy title detail.
     */
    public function detail(): string
    {
        return $this->pluck('trophyTitleDetail');
    }

    /**
     * Gets the trophy title icon URL.
     */
    public function iconUrl(): string
    {
        return $this->pluck('trophyTitleIconUrl');
    }

    /**
     * Gets the trophy title name.
     */
    public function name(): string
    {
        return $this->pluck('trophyTitleName');
    }

    /**
     * Gets the trophy title platform.
     */
    public function platform(): ConsoleType
    {
        return ConsoleType::from($this->pluck('platform'));
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

    public function definedTrophies()
    {
        return $this->pluck('definedTrophies');
    }

    /**
     * Gets the trophy title information from the API.
     */
    public function fetch(): object
    {
        return $this->get(
            'trophy/v1/npCommunicationIds/' . $this->npCommunicationId  . '/trophyGroups',
            [
                'npServiceName' => $this->serviceName()
            ]
        );
    }
}
