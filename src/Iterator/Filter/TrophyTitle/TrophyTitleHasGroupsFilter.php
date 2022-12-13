<?php

namespace Tustin\PlayStation\Iterator\Filter\TrophyTitle;

class TrophyTitleHasGroupsFilter extends \FilterIterator
{
    public function __construct(\Iterator $iterator, private bool $value)
    {
        parent::__construct($iterator);
    }

    /**
     * Checks if the current element of the iterator is acceptable.
     */
    public function accept(): bool
    {
        return $this->current()->hasTrophyGroups() === $this->value;
    }
}
