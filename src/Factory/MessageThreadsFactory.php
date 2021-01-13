<?php
namespace Tustin\PlayStation\Factory;

use Iterator;
use Carbon\Carbon;
use IteratorAggregate;
use Tustin\PlayStation\Api;
use Tustin\PlayStation\Model\MessageThread;
use Tustin\PlayStation\Iterator\MessageThreadsIterator;
use Tustin\PlayStation\Iterator\Filter\ThreadMembersFilter;

class MessageThreadsFactory extends Api implements IteratorAggregate
{
    private $with = [];
    private $only = false;
    private ?Carbon $since = null;

    /**
     * Filters threads that contains the user(s).
     * 
     * Chain this with MessageThreadsFactory::only to ensure you only get threads with these exact users.
     *
     * @param string ...$onlineIds
     * @return MessageThreadsFactory
     */
    public function with(string ...$onlineIds) : MessageThreadsFactory
    {
        $this->with = array_merge($this->with, $onlineIds);

        return $this;
    }

    /**
     * Should be used with the MessageThreadsFactory::with method.
     * 
     * Will return threads that contain ONLY the users passed to MessageThreadsFactory::with.
     *
     * @return MessageThreadsFactory
     */
    public function only() : MessageThreadsFactory
    {
        $this->only = true;

        return $this;
    }

    /**
     * Returns message threads that have only been active since the given date.
     *
     * @param Carbon $date
     * @return MessageThreadsFactory
     */
    public function since(Carbon $date) : MessageThreadsFactory
    {
        $this->since = $date;

        return $this;
    }

    /**
     * Gets the iterator and applies any filters.
     *
     * @return Iterator
     */
    public function getIterator(): Iterator
    {
        $iterator = new MessageThreadsIterator($this);

        if ($this->with)
        {
            $iterator = new ThreadMembersFilter($iterator, $this->with, $this->only);
        }

        return $iterator;
    }

    /**
     * Gets the first message thread in the collection.
     *
     * @return MessageThread
     */
    public function first() : MessageThread
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
    public function getSinceDate() : Carbon
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
    public function create(string ...$onlineIds) : MessageThread
    {
        // We need our onlineId when creating a new group.
        $clientOnlineId = (new UsersFactory($this->getHttpClient()))->me()->onlineId();

        $membersToAdd = [];

        $membersToAdd[] = ['onlineId' => $clientOnlineId];

        foreach ($onlineIds as $onlineId)
        {
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