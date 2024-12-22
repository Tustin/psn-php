<?php

namespace Tustin\PlayStation\Factory;

use Iterator;
use Tustin\PlayStation\Api;
use InvalidArgumentException;
use Tustin\PlayStation\Client;
use Tustin\PlayStation\Model\User;
use Tustin\PlayStation\Enums\LanguageType;
use Tustin\PlayStation\Interfaces\FactoryInterface;
use Tustin\PlayStation\Model\Trophy\UserTrophyTitle;
use Tustin\PlayStation\Exceptions\NoTrophiesException;
use Tustin\PlayStation\Iterator\UserTrophyTitlesIterator;
use Tustin\PlayStation\Iterator\Filter\TrophyTitle\TrophyTitleNameFilter;
use Tustin\PlayStation\Iterator\Filter\TrophyTitle\TrophyTitleHasGroupsFilter;

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


    public function __construct(private User $user, private int $limit = 100)
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
    public function getIterator(): Iterator
    {
        $iterator = new UserTrophyTitlesIterator($this, limit: $this->limit);

        if ($this->withName) {
            $iterator = new TrophyTitleNameFilter($iterator, $this->withName);
        }

        if ($this->hasTrophyGroups !== null) {
            $iterator = new TrophyTitleHasGroupsFilter($iterator, $this->hasTrophyGroups);
        }

        return $iterator;
    }

    /**
     * Gets the current user to get trophies for.
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Gets the current platforms passed to this instance.
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
     */
    public function first(): UserTrophyTitle
    {
        try {
            return $this->getIterator()->current();
        } catch (InvalidArgumentException $e) {
            throw new NoTrophiesException("Client has no trophy titles.");
        }
    }
}
