<?php
namespace Tustin\PlayStation\Iterator\Filter\TrophyGroup;

use Iterator;
use FilterIterator;

class DetailFilter extends FilterIterator
{
    private string $detail;
   
    public function __construct(Iterator $iterator, string $detail)
    {
        parent::__construct($iterator);
        $this->detail = $detail;
    }
   
    public function accept(): bool
    {
        return stripos($this->current()->detail(), $this->detail) !== false;
    }
}