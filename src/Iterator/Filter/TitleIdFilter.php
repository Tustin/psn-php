<?php

namespace Tustin\PlayStation\Iterator\Filter\TrophyTitle;

class TitleIdFilter extends \FilterIterator
{
    public function __construct(\Iterator $iterator, private string $value)
    {
        parent::__construct($iterator);
    }

    public function accept(): bool
    {
        return $this->current()->titleId() === $this->value;
    }
}
