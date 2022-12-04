<?php
namespace Tustin\PlayStation\Iterator\Filter;

use Iterator;
use FilterIterator;

class GroupMembersFilter extends FilterIterator
{
    private array $onlineIds;
    private bool $includesOnly;
   
    public function __construct(Iterator $iterator, array $onlineIds, bool $includesOnly)
    {
        parent::__construct($iterator);
        $this->onlineIds = $onlineIds;
        $this->includesOnly = $includesOnly;
    }
   
    public function accept(): bool
    {
        $thread = $this->current();

        $matchingMembersCount = count(array_filter($this->onlineIds, function($onlineId) use ($thread) {
            return $thread->members()->contains($onlineId);
        }));

        // Redundant, but it helps prevent needing to count each thread's members when theres no reason to.
        if ($matchingMembersCount === 0)
        {
            return false;
        }

        return $this->includesOnly ?
        $matchingMembersCount == $thread->members()->count() - 1 :
        $matchingMembersCount > 0;
    }
}