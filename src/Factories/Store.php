<?php

namespace Tustin\PlayStation\Factories;

use Tustin\PlayStation\Api;
use Tustin\PlayStation\Interfaces\FactoryInterface;
use Tustin\PlayStation\Iterators\StoreSearchIterator;

/**
 * Retrieves information about titles on the PlayStation store.
 */
class Store extends Api implements FactoryInterface
{
    /**
     * Searches for a title on the store.
     */
    public static function search(string $query, int $limit = 20, string $languageCode = 'en', string $countryCode = 'us'): StoreSearchIterator
    {
        return new StoreSearchIterator(
            query: $query,
            limit: $limit,
            languageCode: $languageCode,
            countryCode: $countryCode
        );
    }
}
