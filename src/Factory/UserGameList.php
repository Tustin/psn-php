<?php

namespace Tustin\PlayStation\Factory;

use Iterator;
use IteratorAggregate;
use Tustin\PlayStation\Api;
use InvalidArgumentException;
use Tustin\PlayStation\Client;
use Tustin\PlayStation\Model\User;
use Tustin\PlayStation\Model\GameTitle;
use Tustin\PlayStation\Model\UserGameTitle;
use Tustin\PlayStation\Iterator\GameListIterator;
use Tustin\PlayStation\Interfaces\FactoryInterface;

class UserGameList extends Api implements IteratorAggregate, FactoryInterface
{
    public function __construct(private User $user) {}

    /**
     * Gets the iterator and applies any filters.
     */
    public function getIterator(): Iterator
    {
        $iterator = new GameListIterator($this);

        return $iterator;
    }

    /**
     * Gets the current user for the game list.
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Checks to see if this factory should be looking at a specific user's game list.
     */
    public function hasUser(): bool
    {
        return $this->user !== null;
    }

    /**
     * Gets the first game entry in the collection.
     */
    public function first(): UserGameTitle
    {
        return $this->getIterator()->current();
    }
}
