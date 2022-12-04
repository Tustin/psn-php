<?php

namespace Tustin\PlayStation\Iterator\Filter\User;

use Iterator;
use FilterIterator;

class OnlineIdFilter extends FilterIterator
{
    private string $onlineId;

    public function __construct(Iterator $iterator, string $onlineId)
    {
        parent::__construct($iterator);
        $this->onlineId = $onlineId;
    }

    public function accept(): bool
    {
        return stripos($this->current()->onlineId(), $this->onlineId) !== false;
    }
}
