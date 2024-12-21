<?php

namespace Tustin\PlayStation\Model\Trophy;

use Tustin\PlayStation\Client;
use Tustin\PlayStation\Enums\TrophyServiceName;

class TrophyTitle extends AbstractTrophyTitle
{
    public function __construct(Client $client, string $npCommunicationId, TrophyServiceName $serviceName)
    {
        parent::__construct($client);

        $this->setnpCommunicationId($npCommunicationId);
        $this->setServiceName($serviceName);
    }
    /**
     * Gets the NP communication ID (NPWR_) for this trophy title.
     */
    public function npCommunicationId(): string
    {
        return $this->npCommunicationId;
    }

    /**
     * Gets the trophy service name.
     */
    public function serviceName(): TrophyServiceName
    {
        return $this->serviceName;
    }

    /**
     * Fetches all the trophies for the current trophy title.
     */
    public function fetch(): object
    {
        return $this->get(
            'trophy/v1/npCommunicationIds/' . $this->npCommunicationId()  . '/trophyGroups/all/trophies',
            [
                'npServiceName' => $this->serviceName()
            ]
        );
    }
}
