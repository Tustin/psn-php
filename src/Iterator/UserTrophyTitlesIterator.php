<?php

namespace Tustin\PlayStation\Iterator;

use GuzzleHttp\Client;
use Tustin\PlayStation\Model\User;
use Tustin\PlayStation\Traits\HasUser;
use Tustin\PlayStation\Enum\LanguageType;
use Tustin\PlayStation\Iterator\AbstractApiIterator;
use Tustin\PlayStation\Model\Trophy\UserTrophyTitle;
use Tustin\PlayStation\Iterator\Filter\TrophyTitle\TrophyTitleNameFilter;
use Tustin\PlayStation\Iterator\Filter\TrophyTitle\TrophyTitleHasGroupsFilter;

class UserTrophyTitlesIterator extends AbstractApiIterator
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
    
    public function __construct(Client $client, private User $user)
    {
        parent::__construct($client);

        $this->limit = 100;

        $this->access(0);
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
     */
    public function getLanguage(): LanguageType
    {
        return $this->language ?? LanguageType::English;
    }

    /**
     * Gets the iterator and applies any filters.
     */
    public function getIterator(): \Iterator
    {
        $iterator = $this;

        if ($this->withName) {
            $iterator = new TrophyTitleNameFilter($iterator, $this->withName);
        }

        if (!is_null($this->hasTrophyGroups)) {
            $iterator = new TrophyTitleHasGroupsFilter($iterator, $this->hasTrophyGroups);
        }

        return $iterator;
    }

    /**
     * Accesses a new page of results.
     */
    public function access(mixed $cursor): void
    {
        $body = [
            'limit' => $this->limit,
            'offset' => $cursor,
        ];

        $results = $this->get('trophy/v1/users/' . $this->user()->accountId() . '/trophyTitles', $body);

        $this->update($results->totalItemCount, $results->trophyTitles);
    }

    /**
     * Gets the current user trophy title in the iterator. 
     */
    public function current(): UserTrophyTitle
    {
        $cache = $this->getFromOffset($this->currentOffset);
        $title = new UserTrophyTitle($this->getHttpClient(), $this->user(), $cache->npCommunicationId, $cache->npServiceName);

        return $title->hydrate($cache);
    }
}
