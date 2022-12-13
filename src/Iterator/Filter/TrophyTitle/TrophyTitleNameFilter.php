<?php

namespace Tustin\PlayStation\Iterator\Filter\TrophyTitle;

class TrophyTitleNameFilter extends \FilterIterator
{
    public function __construct(\Iterator $iterator, private string $titleName)
    {
        parent::__construct($iterator);
    }

    /**
     * Checks if the current element of the iterator is acceptable.
     */
    public function accept(): bool
    {
        return stripos($this->current()->name(), $this->titleName) !== false;
    }
}
