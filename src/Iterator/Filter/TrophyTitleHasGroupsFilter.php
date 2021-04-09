<?php

namespace Tustin\PlayStation\Iterator\Filter;

use Iterator;
use FilterIterator;

class TrophyTitleHasGroupsFilter extends FilterIterator
{
    private bool $value;

    public function __construct(Iterator $iterator, bool $value)
    {
        parent::__construct($iterator);
        $this->value = $value;
    }

    public function accept()
    {
        return $this->current()->hasTrophyGroups() === $this->value;
    }
}
