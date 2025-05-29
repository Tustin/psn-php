<?php

namespace Tustin\PlayStation\Factories;

use Tustin\PlayStation\Api;
use Tustin\PlayStation\Models\User;
use Tustin\PlayStation\Models\Group;
use Tustin\PlayStation\Models\MessageThread;
use Tustin\PlayStation\Iterators\GroupsIterator;
use Tustin\PlayStation\Interfaces\FactoryInterface;
use Tustin\PlayStation\Iterators\Filter\GroupMembersFilter;

class Groups extends Api implements \IteratorAggregate, FactoryInterface
{
    private array $with = [];

    private bool $only = false;

    private ?\DateTime $since = null;

    public bool $favorited = false;

    public function __construct(private int $limit = 50) {}

    public static function all(int $limit = 50): self
    {
        return new self($limit);
    }

    /**
     * Filters groups that only contain these onlineIds.
     * 
     * Chain this with GroupsFactory::only to ensure you only get threads with these exact users.
     */
    public function with(string ...$onlineIds): self
    {
        $this->with = array_merge($this->with, $onlineIds);

        return $this;
    }

    /**
     * Should be used with the GroupsFactory::with method.
     * 
     * Will return groups that contain ONLY the users passed to GroupsFactory::with.
     */
    public function only(): self
    {
        $this->only = true;

        return $this;
    }

    /**
     * Filters groups that have only been active since the given date.
     */
    public function since(\DateTime $date): self
    {
        $this->since = $date;

        return $this;
    }

    /**
     * Filters groups that are favorited.
     */
    public function favorited(): self
    {
        $this->favorited = true;

        return $this;
    }

    /**
     * Gets the iterator and applies any filters.
     */
    public function getIterator(): \Iterator
    {
        $iterator = new GroupsIterator($this->limit, $this->favorited);

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
        return $this->since ?? \Carbon\Carbon::createFromTimestamp(0);
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
            $invitees[] = ['accountId' => $user->accountId()];
        }

        $response = $this->postJson('gamingLoungeGroups/v1/groups', [
            'invitees' => $invitees
        ]);

        return new MessageThread($response->groupId, $response->mainThread->threadId);
    }
}
