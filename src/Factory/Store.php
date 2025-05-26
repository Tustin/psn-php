<?php

namespace Tustin\PlayStation\Factory;

use Tustin\PlayStation\Api;
use Tustin\PlayStation\Interfaces\FactoryInterface;
use Tustin\PlayStation\Iterator\StoreSearchIterator;

class Store extends Api implements FactoryInterface
{
    /**
     * Searches for a title on the store.
     */
    public static function search(string $query): StoreSearchIterator
    {
        return new StoreSearchIterator($query);
    }
}
