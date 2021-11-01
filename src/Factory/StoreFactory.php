<?php

namespace Tustin\PlayStation\Factory;

use Tustin\PlayStation\Api;
use Tustin\PlayStation\Model\User;
use Tustin\PlayStation\Interfaces\FactoryInterface;
use Tustin\PlayStation\Iterator\StoreSearchIterator;
use Tustin\PlayStation\Iterator\UsersSearchIterator;

class StoreFactory extends Api implements FactoryInterface
{
    /**
     * Searches for a title on the store.
     *
     * @param string $query
     * @param array $searchFields
     * @return UsersSearchIterator
     */
    public function search(string $query): StoreSearchIterator
    {
        return new StoreSearchIterator($this, $query);
    }
}