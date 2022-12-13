<?php

namespace Tustin\PlayStation\Iterator\Filter\User;

class OnlineIdFilter extends \FilterIterator
{
    public function __construct(\Iterator $iterator, private string $onlineId)
    {
        parent::__construct($iterator);
    }

    /**
     * Checks if the current element of the iterator is acceptable.
     */
    public function accept(): bool
    {
        return stripos($this->current()->onlineId(), $this->onlineId) !== false;
    }
}
