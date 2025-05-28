<?php

namespace Tustin\PlayStation\Model\Store;

use Carbon\Carbon;
use Tustin\PlayStation\Model;
use Tustin\PlayStation\Enums\DescriptionType;

class Concept extends Model
{
    public function __construct(private string $conceptId)
    {
        parent::__construct();
    }

    /**
     * Creates a new concept from existing data.
     */
    public static function fromObject(string $conceptId, object $data): self
    {
        return (new Concept($conceptId))
            ->setCache($data);
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
     * Gets the concept's title id. (ex: PPSA01649_00)
     */
    public function titleId(): string
    {
        return $this->pluck('defaultProduct.npTitleId');
    }

    /**
     * Gets the concept's product SKU. (ex: UP0002-PPSA01649_00-CODBO6CROSSGEN01)
     */
    public function sku(): string
    {
        return $this->pluck('defaultProduct.id');
    }

    /**
     * Gets the concept's platforms.
     *
     * @return array<string>
     */
    public function platforms(): array
    {
        return $this->pluck('platforms');
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

        foreach ($this->pluck('combinedLocalizedGenres') ?? [] as $genre) {
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
    public function descriptionByType(DescriptionType $type): ?string
    {
        foreach ($this->pluck('descriptions') as $description) {
            if (array_key_exists('type', $description) && $description['type'] === $type->value) {
                return $description['desc'];
            }
        }

        return null;
    }

    /**
     * Gets the concept's media.
     */
    public function media(): array
    {
        return $this->pluck('media');
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
