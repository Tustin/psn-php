<?php

namespace Tustin\PlayStation\Model\Trophy;

use GuzzleHttp\Client;
use Tustin\PlayStation\Model;
use Tustin\PlayStation\Factory\TrophyGroupsFactory;

abstract class AbstractTrophyTitle extends Model
{
	protected string $serviceName;

	public function __construct(protected Client $httpClient, private string $npCommunicationId)
	{
	}

	/**
	 * Sets the service name for this trophy title.
	 */
	protected function setServiceName(string $serviceName)
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
	public function npCommunicationId(): string
	{
		return $this->npCommunicationId ??= $this->pluck('npCommunicationId');
	}

	/**
	 * Gets the service name for this trophy title.
	 * 
	 * PS5 has a different service name than PS4 so this needs to be set correctly to avoid errors.
	 */
	public function serviceName(): string
	{
		return $this->serviceName ??= $this->pluck('npServiceName');
	}
}
