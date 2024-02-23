<?php

namespace Tustin\PlayStation;

use Iterator;
use Tustin\PlayStation\Interfaces\Searchable;
use Tustin\PlayStation\Interfaces\SearchRequest;
use Tustin\PlayStation\Search\UserSearchRequest;
use Tustin\PlayStation\Iterator\UsersSearchIterator;

class User extends Api implements Searchable
{
    use Concerns\Get;

    /** @property string $accountId */
    /** @property string $onlineId */

    public static function getBaseUrl(): ?string
    {
        return Client::$apiBaseUrl . '/userProfile/v1/internal/users/';
    }

    public static function getInstanceUrl(string $id): ?string
    {
        return static::getBaseUrl() . $id . '/profiles';
    }

    public static function getSearchUri(): string
    {
        return Client::$apiBaseUrl . '/search/v1/universalSearch';
    }

    /**
     * Performs a search for users based on the query.
     */
    public static function search(string $query): UsersSearchIterator
    {
        return static::performSearch(new UserSearchRequest($query));
    }

    /**
     * Performs a search for users based on the search request.
     */
    public static function performSearch(SearchRequest $searchRequest): Iterator
    {
        return new UsersSearchIterator(
            $searchRequest
        );
    }
}
