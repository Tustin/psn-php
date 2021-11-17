<?php

namespace Tustin\PlayStation\Iterator\Filter\TrophyTitle;

use Iterator;
use FilterIterator;

class TitleIdFilter extends FilterIterator
{
    private string $value;

    public function __construct(Iterator $iterator, string $value)
    {
        parent::__construct($iterator);
        $this->value = $value;
    }

    public function accept()
    {
        return $this->current()->titleId() === $this->value;
    }
}
