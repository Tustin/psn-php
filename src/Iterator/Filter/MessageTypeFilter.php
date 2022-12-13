<?php

namespace Tustin\PlayStation\Iterator\Filter;

class MessageTypeFilter extends \FilterIterator
{
    public function __construct(\Iterator $iterator, private string $type)
    {
        parent::__construct($iterator);
    }

    /**
     * Checks if the current element of the iterator is acceptable.
     */
    public function accept(): bool
    {
        $a = $this->current();
        $b = $this->type;
        return $a instanceof $b;
    }
}
