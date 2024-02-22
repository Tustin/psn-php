<?php

namespace Tustin\PlayStation\Concerns;

use Tustin\PlayStation\ApiRequest;
use Tustin\PlayStation\OAuthToken;
use Tustin\PlayStation\SearchResult;
use Tustin\PlayStation\Iterator\UsersSearchIterator;

/**
 * Searches for a specific record from an API endpoint.
 */
trait Search
{
    public static function search(array $options): \Iterator
    {
        $request = new ApiRequest(
            accessToken: OAuthToken::accessToken(),
            apiBaseUrl: static::getBaseUrl(),
        );

        $response = $request->get(
            static::getSearchUri(),
            [
                'age' => '99',
                'countryCode' => 'us',
            ]
        );

        return new UsersSearchIterator(
            new SearchResult($response->json())
        );
    }
}
