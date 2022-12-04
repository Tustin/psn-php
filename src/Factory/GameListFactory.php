<?php
namespace Tustin\PlayStation\Factory;

use Iterator;
use IteratorAggregate;
use Tustin\PlayStation\Api;
use InvalidArgumentException;
use Tustin\PlayStation\Model\User;
use Tustin\PlayStation\Model\GameTitle;
use Tustin\PlayStation\Interfaces\FactoryInterface;
use Tustin\PlayStation\Iterator\GameListIterator;

class GameListFactory extends Api implements IteratorAggregate, FactoryInterface
{
    public function __construct(private ?User $user)
    {
        parent::__construct($user->getHttpClient());
    }

    /**
     * Filters game list to only get games for the specific user.
     *
     * @param User $user
     * @return GameListFactory
     */
    public function forUser(User $user): GameListFactory
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
        $iterator = new GameListIterator($this);

        return $iterator;
    }

    /**
     * Gets the current user (if specified) to get game list for.
     *
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * Checks to see if this factory should be looking at a specific user's game list.
     *
     * @return boolean
     */
    public function hasUser(): bool
    {
        return $this->user !== null;
    }

    /**
     * Gets the first game entry in the collection.
     *
     * @return GameTitle
     */
    public function first(): GameTitle
    {
        try
        {
            return $this->getIterator()->current();
        }
        catch (InvalidArgumentException $e)
        {
            throw $e;
        }
    }
}