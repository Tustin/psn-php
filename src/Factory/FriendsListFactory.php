<?php
namespace Tustin\PlayStation\Factory;

use Iterator;
use IteratorAggregate;
use Tustin\PlayStation\Api;
use Tustin\PlayStation\Model\User;
use Tustin\PlayStation\Interfaces\FactoryInterface;
use Tustin\PlayStation\Iterator\FriendsListIterator;
use Tustin\PlayStation\Iterator\Filter\User\OnlineIdFilter;
use Tustin\PlayStation\Iterator\Filter\User\CloseFriendFilter;
use Tustin\PlayStation\Iterator\Filter\User\VerifiedUserFilter;

class FriendsListFactory extends Api implements IteratorAggregate, FactoryInterface
{
    private string $onlineId = '';
    private bool $useCloseFriends = false;
    private bool $verified = false;

    public function __construct(private User $user)
    {
        parent::__construct($user->getHttpClient());
    }

    /**
     * Applies the filter for only querying close friends.
     *
     * @return FriendsListFactory
     */
    public function closeFriends(): FriendsListFactory
    {
        $this->useCloseFriends = true;

        return $this;
    }

    /**
     * Applies a filter for only querying users containing this online id.
     *
     * @param string $onlineId
     * @return FriendsListFactory
     */
    public function onlineIdContains(string $onlineId): FriendsListFactory
    {
        $this->onlineId = $onlineId;

        return $this;
    }

    /**
     * Applies a filter for only querying verified friends.
     *
     * @return FriendsListFactory
     */
    public function verified(): FriendsListFactory
    {
        $this->verified = true;

        return $this;
    }

    /**
     * Gets the iterator and applies any filters.
     *
     * @return Iterator
     */
    public function getIterator(): Iterator
    {
        $iterator = new FriendsListIterator($this, $this->user->accountId());

        if ($this->useCloseFriends)
        {
            $iterator = new CloseFriendFilter($iterator);
        }

        if ($this->verified)
        {
            $iterator = new VerifiedUserFilter($iterator);
        }

        if ($this->onlineId)
        {
            $iterator = new OnlineIdFilter($iterator, $this->onlineId);
        }

        return $iterator;
    }

    /**
     * Gets the first friend.
     *
     * @return User
     */
    public function first(): User
    {
        return $this->getIterator()->current();
    }
}