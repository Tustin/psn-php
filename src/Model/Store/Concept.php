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

    /**
     * Gets the title's release date.
     *
     * @return \DateTime
     */
    public function releaseDate(): \DateTime
    {
        return Carbon::parse($this->pluck('releaseDate.value'));
    }

    /**
     * Gets a list of the title's genres.
     *
     * @return array
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
     * Gets the titles's long-form description.
     * 
     * @return string
     */
    public function longDescription(): string
    {
        return $this->descriptionByType(DescriptionType::Long);
    }

    /**
     * Gets the titles's short-form description.
     *
     * @return string
     */
    public function shortDescription(): string
    {
        return $this->descriptionByType(DescriptionType::Short);
    }

    /**
     * Gets a specific description by type.
     *
     * @param DescriptionType $type
     * @return string
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

	public function fetch(): object
    {
        return $this->graphql('metGetConceptById', [
            'conceptId' => $this->conceptId(),
            'productId' => ''
        ])->conceptRetrieve;
    }
}