<?php
namespace Tustin\PlayStation\Model;

use GuzzleHttp\Client;
use Tustin\PlayStation\AbstractTrophyTitle;

class TrophyTitle extends AbstractTrophyTitle
{

	public function __construct(Client $client, string $npCommunicationId)
	{
		parent::__construct($client);

		$this->setNpCommuncationId($npCommunicationId);
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
}