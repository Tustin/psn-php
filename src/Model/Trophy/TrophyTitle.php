<?php
namespace Tustin\PlayStation\Model\Trophy;

use GuzzleHttp\Client;

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
    public function npCommunicationId(): string
    {
        return $this->npCommuncationId;
	}

    /**
     * Gets the trophy service name.
     *
     * @return string
     */
	public function serviceName(): string
    {
        return $this->serviceName;
	}

    // @TODO: Implement
    public function fetch(): object
    {
        throw new \BadMethodCallException();
    }
}