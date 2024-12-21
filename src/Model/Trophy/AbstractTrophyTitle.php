<?php

namespace Tustin\PlayStation\Model\Trophy;

use Tustin\PlayStation\Model;
use Tustin\PlayStation\Enums\TrophyServiceName;
use Tustin\PlayStation\Factory\TrophyGroupsFactory;

/**
 * This class exists because as of today, Sony only gives you useful trophy title information if you get trophy titles from a user's profile.
 * 
 * There is no known endpoint that gives you trophy title information solely using the npCommunicationId.
 * 
 * For now, we'll have two seperate classes for each instance of a trophy title (one with actual info and one with nothing),
 * and hope that in the future, Sony will make an endpoint that can give trophy title information.
 * 
 * - Tustin, Jan 11, 2021
 */
abstract class AbstractTrophyTitle extends Model
{
    protected string $npCommunicationId;

    protected TrophyServiceName $serviceName;

    /**
     * Sets the NP communication ID (NPWR_) for this trophy title.
     */
    protected function setnpCommunicationId(string $npCommunicationId): void
    {
        $this->npCommunicationId = $npCommunicationId;
    }

    /**
     * Sets the service name for this trophy title.
     */
    protected function setServiceName(TrophyServiceName $serviceName): void
    {
        $this->serviceName = $serviceName;
    }

    /**
     * Gets all the trophy groups for the trophy title.
     */
    public function trophyGroups(): TrophyGroupsFactory
    {
        return new TrophyGroupsFactory($this);
    }

    /**
     * Gets the NP communication ID (NPWR_) for this trophy title.
     */
    public abstract function npCommunicationId(): string;

    /**
     * Gets the service name for this trophy title.
     */
    public abstract function serviceName(): TrophyServiceName;
}
