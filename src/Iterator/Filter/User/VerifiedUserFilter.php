<?php

namespace Tustin\PlayStation\Iterator\Filter\User;

class VerifiedUserFilter extends \FilterIterator
{
    public function __construct(\Iterator $iterator)
    {
        parent::__construct($iterator);
    }

    /**
     * Returns whether the current element of the iterator is valid.
     */
    public function accept(): bool
    {
        return $this->current()->isVerified() === true;
    }
}
