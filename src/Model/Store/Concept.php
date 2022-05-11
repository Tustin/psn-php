<?php
namespace Tustin\PlayStation\Model\Store;

use GuzzleHttp\Client;
use Tustin\PlayStation\Model;

class Concept extends Model
{
    private string $conceptId;
    
	public function __construct(Client $client, string $conceptId)
	{
		parent::__construct($client);

		$this->conceptId = $conceptId;
	}

    public static function fromObject(Client $client, object $data)
    {
        $instance = new Concept($client, $data->conceptId);
        $instance->setCache($data);

        return $instance;
    }

    public function productId(): string
    {
        return $this->issetPluck('id');
    }

    public function name(): string
    {
        return $this->issetPluck('name');
    }

    public function conceptId(): string
    {
        return $this->conceptId;
    }

    public function publisher(): string
    {
        return ($this->issetPluck('publisherName') ?? $this->issetPluck('leadPublisherName'));
    }

	public function fetch(): object
    {
        return $this->graphql('metGetConceptById', [
            'conceptId' => $this->conceptId(),
            'productId' => ''
        ])->conceptRetrieve;
    }
}