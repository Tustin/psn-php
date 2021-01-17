<?php

namespace Tustin\PlayStation\Factory;

use Iterator;
use Carbon\Carbon;
use IteratorAggregate;
use Tustin\PlayStation\Api;
use Tustin\PlayStation\Model\Group;
use Tustin\PlayStation\Model\MessageThread;
use Tustin\PlayStation\Iterator\GroupsIterator;
use Tustin\PlayStation\Iterator\Filter\GroupMembersFilter;

class GroupsFactory extends Api implements IteratorAggregate
{
    private $with = [];

    /**
     * @var boolean
     */
    private $only = false;

    /**
     * @var Carbon|null
     */
    private $since = null;

    public $favorited = false;

    /**
     * Filters groups that only contain these onlineIds.
     * 
     * Chain this with GroupsFactory::only to ensure you only get threads with these exact users.
     *
     * @param string ...$onlineIds
     * @return GroupsFactory
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
     *
     * @return GroupsFactory
     */
    public function only(): GroupsFactory
    {
        $this->only = true;

        return $this;
    }

    /**
     * Filters groups that have only been active since the given date.
     *
     * @param Carbon $date
     * @return GroupsFactory
     */
    public function since(Carbon $date): GroupsFactory
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
     *
     * @return Iterator
     */
    public function getIterator(): Iterator
    {
        $iterator = new GroupsIterator($this);

        if ($this->with) {
            $iterator = new GroupMembersFilter($iterator, $this->with, $this->only);
        }

        return $iterator;
    }

    /**
     * Gets the first group in the collection.
     *
     * @return Group
     */
    public function first(): Group
    {
        return $this->getIterator()->current();
    }

    /**
     * The date to get messages since then.
     * 
     * Returns unix epoch if not set prior.
     *
     * @return Carbon
     */
    public function getSinceDate(): Carbon
    {
        return $this->since ?? Carbon::createFromTimestamp(0);
    }

    /**
     * Creates a new message thread.
     * 
     * Will return an existing message thread if a thread already exists containing the same users you pass to this method.
     * 
     * @TODO: Update for new API
     *
     * @param string ...$onlineIds
     * @return MessageThread
     */
    public function create(string ...$onlineIds): MessageThread
    {
        // We need our onlineId when creating a new group.
        $clientOnlineId = (new UsersFactory($this->getHttpClient()))->me()->onlineId();

        $membersToAdd = [];

        $membersToAdd[] = ['onlineId' => $clientOnlineId];

        foreach ($onlineIds as $onlineId) {
            $membersToAdd[] = ['onlineId' => $onlineId];
        }

        $response = $this->postMultiPart('https://us-gmsg.np.community.playstation.net/groupMessaging/v1/threads/', [
            [
                'name' => 'threadDetail',
                'contents' => json_encode([
                    'threadDetail' => [
                        'threadMembers' => $membersToAdd
                    ]
                ], JSON_PRETTY_PRINT),
                'headers' => [
                    'Content-Type' => 'application/json; charset=utf-8'
                ]
            ]
        ]);

        return new MessageThread($this->httpClient, $response->threadId);
    }
}
