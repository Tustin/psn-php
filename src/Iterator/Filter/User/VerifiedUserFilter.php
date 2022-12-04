<?php

namespace Tustin\PlayStation\Iterator\Filter\User;

use Iterator;
use FilterIterator;

class VerifiedUserFilter extends FilterIterator
{
    public function __construct(Iterator $iterator)
    {
        parent::__construct($iterator);
    }

    public function accept(): bool
    {
        return $this->current()->isVerified() === true;
    }
}
