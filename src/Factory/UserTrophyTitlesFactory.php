<?php

namespace Tustin\PlayStation\Factory;

use GuzzleHttp\Client;
use Tustin\PlayStation\Api;
use Tustin\PlayStation\Traits\HasUser;
use Tustin\PlayStation\Enum\LanguageType;
use Tustin\PlayStation\Interfaces\FactoryInterface;
use Tustin\PlayStation\Model\Trophy\UserTrophyTitle;
use Tustin\PlayStation\Iterator\UserTrophyTitlesIterator;
use Tustin\PlayStation\Iterator\Filter\TrophyTitle\TrophyTitleNameFilter;
use Tustin\PlayStation\Iterator\Filter\TrophyTitle\TrophyTitleHasGroupsFilter;

class UserTrophyTitlesFactory extends Api implements \IteratorAggregate, FactoryInterface
{
    use HasUser;

    /**
     * Platforms for filtering.
     */
    protected array $platforms = [];

    /**
     * The trophy title name for filtering.
     */
    private string $withName = '';

    /**
     * Filter property for having trophy groups.
     * 
     * We want this to be null by default so that if the client doesn't call hasTrophyGroups, it will return all titles.
     */
    private ?bool $hasTrophyGroups = null;

    public function __construct(Client $client)
    {
        parent::__construct($client);
    }

    /**
     * Filters trophy titles that either have trophy groups or no trophy groups.
     */
    public function hasTrophyGroups(bool $value = true): self
    {
        $this->hasTrophyGroups = $value;

        return $this;
    }

    /**
     * Filters trophy titles to only get titles containing the supplied name.
     */
    public function withName(string $name): self
    {
        $this->withName = $name;

        return $this;
    }

    /**
     * Gets the iterator and applies any filters.
     */
    public function getIterator(): \Iterator
    {
        $iterator = new UserTrophyTitlesIterator($this);

        if ($this->withName) {
            $iterator = new TrophyTitleNameFilter($iterator, $this->withName);
        }

        if (!is_null($this->hasTrophyGroups)) {
            $iterator = new TrophyTitleHasGroupsFilter($iterator, $this->hasTrophyGroups);
        }

        return $iterator;
    }

    /**
     * Gets the current platforms passed to this instance.
     *
     * @return array
     */
    public function getPlatforms(): array
    {
        return $this->platforms;
    }

    /**
     * Gets the current language passed to this instance.
     * 
     * If the language has not been set prior, this will return LanguageType::English.
     *
     * @return LanguageType
     */
    public function getLanguage(): LanguageType
    {
        return $this->language ?? LanguageType::English;
    }

    /**
     * Gets the first trophy title in the collection.
     */
    public function first(): UserTrophyTitle
    {
        return $this->getIterator()->current();
    }
}
