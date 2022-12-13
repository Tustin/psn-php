<?php

namespace Tustin\PlayStation\Iterator\Filter\TrophyGroup;

class NameFilter extends \FilterIterator
{
    public function __construct(\Iterator $iterator, private string $groupName)
    {
        parent::__construct($iterator);
    }
   
    /**
     * Checks if the current element of the iterator is acceptable.
     */
    public function accept(): bool
    {
        return stripos($this->current()->name(), $this->groupName) !== false;
    }
}