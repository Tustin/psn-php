<?php

namespace Tustin\PlayStation\Iterator\Filter\User;

use Iterator;
use FilterIterator;

class CloseFriendFilter extends FilterIterator
{
    public function __construct(Iterator $iterator)
    {
        parent::__construct($iterator);
    }

    public function accept(): bool
    {
        // Kind of a hack so we don't need to fetch the whole user's profile for each iteration.
        // This property will only exist if the user is a close friend, otherwise it is completely omitted.
        // Might be due for a refactor at some point, but should be okay for now...
        // Tustin, November 16, 2021.
        return array_key_exists('personalDetail', $this->current()->getCache());
    }
}
