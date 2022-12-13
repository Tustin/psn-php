<?php
namespace Tustin\PlayStation\Model\Store;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Tustin\PlayStation\Model;
use Tustin\PlayStation\Enum\DescriptionType;

class Concept extends Model
{
	public function __construct(Client $client, private string $conceptId)
	{
		parent::__construct($client);
	}

    /**
     * Creates a new concept from existing data.
     */
    public static function fromObject(Client $client, object $data): self
    {
        $instance = new Concept($client, $data->conceptId);
        $instance->setCache($data);

        return $instance;
    }

    /**
     * Gets the concept's product id.
     */
    public function productId(): string
    {
        return $this->pluck('id');
    }

    /**
     * Gets the concept's name.
     */
    public function name(): string
    {
        return $this->pluck('name');
    }

    /**
     * Gets the concept's id.
     */
    public function conceptId(): string
    {
        return $this->conceptId;
    }

    /**
     * Gets the concept's publicher.
     */
    public function publisher(): string
    {
        return ($this->pluck('publisherName') ?? $this->pluck('leadPublisherName'));
    }

    /**
     * Gets the concept's release date.
     */
    public function releaseDate(): \DateTime
    {
        return Carbon::parse($this->pluck('releaseDate.value'));
    }

    /**
     * Gets a list of the concept's genres.
     *
     * @return array<string>
     */
    public function genres(): array
    {
        $genres = [];

        foreach ($this->pluck('combinedLocalizedGenres') ?? [] as $genre)
        {
            $genres[] = $genre['value'];
        }

        return $genres;
    }

    /**
     * Gets the concept's long-form description.
     */
    public function longDescription(): string
    {
        return $this->descriptionByType(DescriptionType::Long);
    }

    /**
     * Gets the concept's short-form description.
     */
    public function shortDescription(): string
    {
        return $this->descriptionByType(DescriptionType::Short);
    }

    /**
     * Gets a specific description by type.
     */
    public function descriptionByType(DescriptionType $type): string
    {
        foreach ($this->pluck('descriptions') as $description)
        {
            if ($description['type'] === $type->value)
            {
                return $description['value'];
            }
        }

        return '';
    }

    /**
     * Fetches the concept's information from the API.
     */
	public function fetch(): object
    {
        return $this->graphql('metGetConceptById', [
            'conceptId' => $this->conceptId(),
            'productId' => ''
        ])->conceptRetrieve;
    }
}