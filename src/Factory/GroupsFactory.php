<?php

namespace Tustin\PlayStation\Factory;

use Carbon\Carbon;
use Tustin\PlayStation\Api;
use Tustin\PlayStation\Model\User;
use Tustin\PlayStation\Model\Group;
use Tustin\PlayStation\Model\MessageThread;
use Tustin\PlayStation\Iterator\GroupsIterator;
use Tustin\PlayStation\Iterator\Filter\GroupMembersFilter;

class GroupsFactory extends Api implements \IteratorAggregate
{
    private array $with = [];

    private bool $only = false;

    private ?Carbon $since = null;

    public bool $favorited = false;

    /**
     * Filters groups that only contain these onlineIds.
     * 
     * Chain this with GroupsFactory::only to ensure you only get threads with these exact users.
     */
    public function with(string ...$onlineIds): GroupsFactory
    {
        $this->with = array_merge($this->with, $onlineIds);

        return $this;
    }

    /**
     * Should be used with the GroupsFactory::with method.
     * 
     * Will return groups that contain ONLY the users passed to GroupsFactory::with.
     */
    public function only(): GroupsFactory
    {
        $this->only = true;

        return $this;
    }

    /**
     * Filters groups that have only been active since the given date.
     */
    public function since(\DateTime $date): GroupsFactory
    {
        $this->since = $date;

        return $this;
    }

    /**
     * Filters groups that are favorited.
     *
     * @return GroupsFactory
     */
    public function favorited(): GroupsFactory
    {
        $this->favorited = true;

        return $this;
    }

    /**
     * Gets the iterator and applies any filters.
     */
    public function getIterator(): \Iterator
    {
        $iterator = new GroupsIterator($this);

        if ($this->with) {
            $iterator = new GroupMembersFilter($iterator, $this->with, $this->only);
        }

        return $iterator;
    }

    /**
     * Gets the first group in the collection.
     */
    public function first(): Group
    {
        return $this->getIterator()->current();
    }

    /**
     * The date to get messages since then.
     * 
     * Returns unix epoch if not set prior.
     */
    public function getSinceDate(): \DateTime
    {
        return $this->since ?? Carbon::createFromTimestamp(0);
    }

    /**
     * Creates a new message thread.
     * 
     * Will return an existing message thread if a thread already exists containing the same users you pass to this method.
     */
    public function create(User ...$users): MessageThread
    {
        $invitees = [];

        foreach ($users as $user) {
            $invitees[] = ['accountId' => $user->accountId()]; // TODO: Test if onlineId can still be used here.
        }

        $response = $this->postJson('gamingLoungeGroups/v1/groups', [
            'invitees' => $invitees
        ]);

        return new MessageThread(new Group($this, $response->groupId), $response->mainThread->threadId);
    }
}
