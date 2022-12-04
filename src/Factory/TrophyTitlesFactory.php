<?php

namespace Tustin\PlayStation\Factory;

use Iterator;
use IteratorAggregate;
use Tustin\PlayStation\Api;
use InvalidArgumentException;
use Tustin\PlayStation\Model\User;
use Tustin\PlayStation\Enum\LanguageType;
use Tustin\PlayStation\Interfaces\FactoryInterface;
use Tustin\PlayStation\Model\Trophy\UserTrophyTitle;
use Tustin\PlayStation\Exception\NoTrophiesException;
use Tustin\PlayStation\Iterator\TrophyTitlesIterator;
use Tustin\PlayStation\Iterator\Filter\TrophyTitle\TrophyTitleNameFilter;
use Tustin\PlayStation\Iterator\Filter\TrophyTitle\TrophyTitleHasGroupsFilter;

class TrophyTitlesFactory extends Api implements IteratorAggregate, FactoryInterface
{
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

    public function __construct(private ?User $user)
    {
        parent::__construct($user->getHttpClient());

        $this->user = $user;
    }

    /**
     * Filters trophy titles that either have trophy groups or no trophy groups.
     *
     * @param boolean $value
     * @return TrophyTitlesFactory
     */
    public function hasTrophyGroups(bool $value = true): TrophyTitlesFactory
    {
        $this->hasTrophyGroups = $value;

        return $this;
    }

    /**
     * Filters trophy titles to only get titles containing the supplied name.
     *
     * @param string $name
     * @return TrophyTitlesFactory
     */
    public function withName(string $name): TrophyTitlesFactory
    {
        $this->withName = $name;

        return $this;
    }

    /**
     * Filters trophy titles to only get trophies for the specific user.
     *
     * @param User $user
     * @return TrophyTitlesFactory
     */
    public function forUser(User $user): TrophyTitlesFactory
    {
        $this->user = $user;

        return $this;
    }
    /**
     * Gets the iterator and applies any filters.
     *
     * @return Iterator
     */
    public function getIterator(): Iterator
    {
        $iterator = new TrophyTitlesIterator($this);

        if ($this->withName) {
            $iterator = new TrophyTitleNameFilter($iterator, $this->withName);
        }

        if (!is_null($this->hasTrophyGroups)) {
            $iterator = new TrophyTitleHasGroupsFilter($iterator, $this->hasTrophyGroups);
        }

        return $iterator;
    }

    /**
     * Gets the current user (if specified) to get trophies for.
     *
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Checks to see if this factory should be looking at a specific user's trophies.
     *
     * @return boolean
     */
    public function hasUser(): bool
    {
        return $this->user !== null;
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
     *
     * @return UserTrophyTitle
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
