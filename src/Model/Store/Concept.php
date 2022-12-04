<?php
namespace Tustin\PlayStation\Model\Store;

use GuzzleHttp\Client;
use Tustin\PlayStation\Model;

class Concept extends Model
{
	public function __construct(Client $client, private string $conceptId)
	{
		parent::__construct($client);
	}

    public static function fromObject(Client $client, object $data)
    {
        $instance = new Concept($client, $data->conceptId);
        $instance->setCache($data);

        return $instance;
    }

    public function productId(): string
    {
        return $this->pluck('id');
    }

    public function name(): string
    {
        return $this->pluck('name');
    }

    public function conceptId(): string
    {
        return $this->conceptId;
    }

    public function publisher(): string
    {
        return ($this->pluck('publisherName') ?? $this->pluck('leadPublisherName'));
    }

	public function fetch(): object
    {
        return $this->graphql('metGetConceptById', [
            'conceptId' => $this->conceptId(),
            'productId' => ''
        ])->conceptRetrieve;
    }
}