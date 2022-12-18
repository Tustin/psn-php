<?php

namespace Tustin\PlayStation\Factory;

use Tustin\PlayStation\Model\User;
use Tustin\PlayStation\Model\Group;

class GroupMembersFactory implements \IteratorAggregate, \Countable
{
    /**
     * The name to filter with.
     */
    private string $name;

    public function __construct(private Group $group)
    {
    }

    /**
     * Returns only members with a name containing the supplied value.
     */
    public function withName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Returns whether or not a member with the onlineId exists in this thread.
     */
    public function contains(string $onlineId): bool
    {
        foreach ($this as $member) {
            if (strcasecmp($member->onlineId(), $onlineId) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns whether or not this thread contains only the user supplied and the client.
     */
    public function containsOnly(string $onlineId): bool
    {
        return $this->contains($onlineId) && $this->count() === 2;
    }

    /**
     * Gets the iterator and applies any filters.
     */
    public function getIterator(): \Iterator
    {
        $iterator = yield from array_map(
            fn ($member) => new User($this->group->getHttpClient(), $member['accountId']),
            $this->group->membersArray()
        );

        if ($this->name) {
            $iterator = new \CallbackFilterIterator(
                $iterator,
                fn ($it) => stripos($it->onlineId(), $this->name) !== false
            );
        }

        return $iterator;
    }

    /**
     * Gets the count of members in this thread.
     */
    public function count(): int
    {
        return \count($this->messageThread->membersArray());
    }
}
