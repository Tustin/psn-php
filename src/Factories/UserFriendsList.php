<?php

namespace Tustin\PlayStation\Factories;

use Tustin\PlayStation\Api;
use Tustin\PlayStation\Models\User;
use Tustin\PlayStation\Interfaces\FactoryInterface;
use Tustin\PlayStation\Iterators\FriendsListIterator;
use Tustin\PlayStation\Iterators\Filter\User\OnlineIdFilter;
use Tustin\PlayStation\Iterators\Filter\User\CloseFriendFilter;
use Tustin\PlayStation\Iterators\Filter\User\VerifiedUserFilter;

/**
 * Retrieve a user's friends list.
 */
class UserFriendsList extends Api implements \IteratorAggregate, FactoryInterface
{
    private string $onlineId = '';
    private bool $useCloseFriends = false;
    private bool $verified = false;

    public function __construct(private string $accountId, private int $limit = 100) {}

    /**
     * Applies the filter for only querying close friends.
     */
    public function closeFriends(): self
    {
        $this->useCloseFriends = true;

        return $this;
    }

    /**
     * Applies a filter for only querying users containing this online id.
     */
    public function onlineIdContains(string $onlineId): self
    {
        $this->onlineId = $onlineId;

        return $this;
    }

    /**
     * Applies a filter for only querying verified friends.
     */
    public function verified(): self
    {
        $this->verified = true;

        return $this;
    }

    /**
     * Gets the iterator and applies any filters.
     */
    public function getIterator(): \Iterator
    {
        $iterator = new FriendsListIterator($this->accountId, $this->limit);

        if ($this->useCloseFriends) {
            $iterator = new CloseFriendFilter($iterator);
        }

        if ($this->verified) {
            $iterator = new VerifiedUserFilter($iterator);
        }

        if ($this->onlineId) {
            $iterator = new OnlineIdFilter($iterator, $this->onlineId);
        }

        return $iterator;
    }

    /**
     * Gets the first user in the friends list.
     */
    public function first(): User
    {
        return $this->getIterator()->current();
    }
}
