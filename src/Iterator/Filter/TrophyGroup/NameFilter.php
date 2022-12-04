<?php
namespace Tustin\PlayStation\Iterator\Filter\TrophyGroup;

use Iterator;
use FilterIterator;

class NameFilter extends FilterIterator
{
    private string $groupName;
   
    public function __construct(Iterator $iterator, string $groupName)
    {
        parent::__construct($iterator);
        $this->groupName = $groupName;
    }
   
    public function accept(): bool
    {
        return stripos($this->current()->name(), $this->groupName) !== false;
    }
}