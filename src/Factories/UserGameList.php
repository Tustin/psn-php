<?php

namespace Tustin\PlayStation\Factories;

use Tustin\PlayStation\Api;
use Tustin\PlayStation\Models\UserGameTitle;
use Tustin\PlayStation\Iterators\GameListIterator;
use Tustin\PlayStation\Interfaces\FactoryInterface;

/**
 * Retrieves the user's game list.
 */
class UserGameList extends Api implements \IteratorAggregate, FactoryInterface
{
    public function __construct(private string $accountId, private int $limit = 100) {}

    /**
     * Gets the iterator and applies any filters.
     */
    public function getIterator(): \Iterator
    {
        $iterator = new GameListIterator($this->accountId, $this->limit);

        return $iterator;
    }

    /**
     * Gets the first game entry in the collection.
     */
    public function first(): UserGameTitle
    {
        return $this->getIterator()->current();
    }
}
