<?php

namespace Tustin\PlayStation\Iterator\Filter;

use Iterator;
use FilterIterator;

class MessageTypeFilter extends FilterIterator
{
    private string $type;

    public function __construct(Iterator $iterator, string $type)
    {
        parent::__construct($iterator);
        $this->type = $type;
    }

    public function accept(): bool
    {
        $a = $this->current();
        $b = $this->type;
        return $a instanceof $b;
    }
}
