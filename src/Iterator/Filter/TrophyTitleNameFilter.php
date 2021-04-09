<?php

namespace Tustin\PlayStation\Iterator\Filter;

use Iterator;
use FilterIterator;

class TrophyTitleNameFilter extends FilterIterator
{
    private string $titleName;

    public function __construct(Iterator $iterator, string $titleName)
    {
        parent::__construct($iterator);
        $this->titleName = $titleName;
    }

    public function accept()
    {
        return stripos($this->current()->name(), $this->titleName) !== false;
    }
}
