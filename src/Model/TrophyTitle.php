<?php
namespace Tustin\PlayStation\Model;

use GuzzleHttp\Client;
use Tustin\PlayStation\AbstractTrophyTitle;

class TrophyTitle extends AbstractTrophyTitle
{
	
	public function __construct(Client $client, string $npCommunicationId, string $serviceName = 'trophy')
	{
		parent::__construct($client);

		$this->setNpCommuncationId($npCommunicationId);
		$this->setServiceName($serviceName);
	}
    /**
     * Gets the NP communication ID (NPWR_) for this trophy title.
     *
     * @return string
     */
    public function npCommunicationId() : string
    {
        return $this->npCommuncationId;
	}


	public function serviceName(): string
    {
        return $this->serviceName;
	}
}