<?php
namespace Tustin\PlayStation\Model;

use GuzzleHttp\Client;
use Tustin\PlayStation\Api;
use Tustin\PlayStation\Interfaces\Fetchable;

class StoreItem extends Api implements Fetchable
{
	private $contentId;
	
	public function __construct(Client $client, string $contentId)
	{
		parent::__construct($client);

		$this->contentId = $contentId;
	}


	public function fetch() : object
    {
        return $this->get('userProfile/v1/internal/users/' . $this->accountId . '/profiles');
    }
}