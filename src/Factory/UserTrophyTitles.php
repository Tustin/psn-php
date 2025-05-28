<?php

namespace Tustin\PlayStation\Factory;

use Tustin\PlayStation\Api;
use Tustin\PlayStation\Client;
use Tustin\PlayStation\Enums\LanguageType;
use Tustin\PlayStation\Interfaces\FactoryInterface;
use Tustin\PlayStation\Models\Trophy\UserTrophyTitle;
use Tustin\PlayStation\Iterator\UserTrophyTitlesIterator;
use Tustin\PlayStation\Iterator\Filter\TrophyTitle\TrophyTitleNameFilter;
use Tustin\PlayStation\Iterator\Filter\TrophyTitle\TrophyTitleHasGroupsFilter;

/**
 * Retrieves the user's trophy titles.
 */
class UserTrophyTitles extends Api implements \IteratorAggregate, FactoryInterface
{
    /**
     * Platforms for filtering.
     */
    protected array $platforms = [];

    /**
     * The trophy title name for filtering.
     */
    private ?string $withName = null;

    /**
     * Filter property for having trophy groups.
     * 
     * We want this to be null by default so that if the client doesn't call hasTrophyGroups, it will return all titles.
     */
    private ?bool $hasTrophyGroups = null;


    public function __construct(private string $accountId, private int $limit = 100)
    {
        parent::__construct(
            Client::getInstance()->getHttpClient(),
        );
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
     * Filters trophy titles to only get trophies with the specified name.
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
        $iterator = new UserTrophyTitlesIterator($this->accountId, limit: $this->limit);

        if ($this->withName) {
            $iterator = new TrophyTitleNameFilter($iterator, $this->withName);
        }

        if ($this->hasTrophyGroups !== null) {
            $iterator = new TrophyTitleHasGroupsFilter($iterator, $this->hasTrophyGroups);
        }

        return $iterator;
    }

    /**
     * Gets the current platforms passed to this instance.
     * 
     * @TODO: What was this used for? Maybe for a filter for trophy titles by platform that wasn't implemented?
     */
    public function getPlatforms(): array
    {
        return $this->platforms;
    }

    /** 
     * Gets the current language passed to this instance.
     * 
     * @TODO: language is not a property, do we need this??
     */
    public function getLanguage(): LanguageType
    {
        return $this->language ?? LanguageType::English;
    }

    /**
     * Gets the first trophy title in the collection.
     * 
     * Returns null if no trophy title is found.
     */
    public function first(): ?UserTrophyTitle
    {
        return $this->getIterator()->current();
    }
}
