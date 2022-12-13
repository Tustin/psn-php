<?php

namespace Tustin\PlayStation\Iterator\Filter\TrophyGroup;

class DetailFilter extends \FilterIterator
{
    public function __construct(\Iterator $iterator, private string $detail)
    {
        parent::__construct($iterator);
    }

    /**
     * Checks if the current element of the iterator is acceptable.
     */
    public function accept(): bool
    {
        return stripos($this->current()->detail(), $this->detail) !== false;
    }
}
