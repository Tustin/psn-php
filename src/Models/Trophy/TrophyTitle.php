<?php

namespace Tustin\PlayStation\Models\Trophy;

use Tustin\PlayStation\Enums\TrophyServiceName;
use Tustin\PlayStation\Exceptions\NotFoundHttpException;
use Tustin\PlayStation\Exceptions\TrophyTitle\TrophyTitleNotFound;

class TrophyTitle extends AbstractTrophyTitle
{
    public function __construct(string $npCommunicationId, TrophyServiceName $serviceName = TrophyServiceName::Trophy)
    {
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
        try {
            return $this->get(
                'trophy/v1/npCommunicationIds/' . $this->npCommunicationId()  . '/trophyGroups/all/trophies',
                [
                    'npServiceName' => $this->serviceName()
                ]
            );
        } catch (NotFoundHttpException) {
            throw new TrophyTitleNotFound(
                'Unable to find trophy title with NP communication ID: ' . $this->npCommunicationId() . '. If this is a PS5 title, make sure you set the correct trophy service name.'
            );
        }
    }
}
