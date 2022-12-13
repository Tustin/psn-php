<?php

namespace Tustin\PlayStation\Iterator\Filter;

class GroupMembersFilter extends \FilterIterator
{
    public function __construct(\Iterator $iterator, private array $onlineIds, private bool $includesOnly)
    {
        parent::__construct($iterator);
    }

    /**
     * Checks if the current element of the iterator is acceptable.
     */
    public function accept(): bool
    {
        $thread = $this->current();

        $matchingMembersCount = count(array_filter($this->onlineIds, function ($onlineId) use ($thread) {
            return $thread->members()->contains($onlineId);
        }));

        // Redundant, but it helps prevent needing to count each thread's members when theres no reason to.
        if ($matchingMembersCount === 0) {
            return false;
        }

        return $this->includesOnly ?
            $matchingMembersCount == $thread->members()->count() - 1 :
            $matchingMembersCount > 0;
    }
}
