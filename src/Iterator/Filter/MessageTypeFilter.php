<?php

namespace Tustin\PlayStation\Iterator\Filter;

use Iterator;
use FilterIterator;

class MessageTypeFilter extends FilterIterator
{
    /**
     * @var string
     */
    private $type;

    public function __construct(Iterator $iterator, string $type)
    {
        parent::__construct($iterator);
        $this->type = $type;
    }

    public function accept()
    {
        $a = $this->current();
        $b = $this->type;
        return $a instanceof $b;
    }
}
