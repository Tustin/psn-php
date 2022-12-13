<?php

namespace Tustin\PlayStation\Iterator\Filter\TrophyGroup;

use Tustin\PlayStation\Enum\TrophyType;
use Tustin\PlayStation\Traits\OperandParser;

class TrophyTypeFilter extends \FilterIterator
{
    use OperandParser;

    public function __construct(\Iterator $iterator, private TrophyType $trophyType, private string $operator, private int $count)
    {
        parent::__construct($iterator);
    }

    /**
     * Checks if the current element of the iterator is acceptable.
     */
    public function accept(): bool
    {
        return $this->parse($this->current()->trophyCount($this->trophyType), $this->count);
    }
}
