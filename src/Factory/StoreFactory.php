<?php

namespace Tustin\PlayStation\Factory;

use Tustin\PlayStation\Api;
use Tustin\PlayStation\Interfaces\FactoryInterface;
use Tustin\PlayStation\Iterator\StoreSearchIterator;

class StoreFactory extends Api implements FactoryInterface
{
    /**
     * Searches for a title on the store.
     */
    public function search(string $query): StoreSearchIterator
    {
        return new StoreSearchIterator($this, $query);
    }
}