<?php

namespace Tests\Traits;

use Tustin\PlayStation\Traits\OperandParser;

class OperandParserTester
{
    use OperandParser;

    public function __construct(string $operator)
    {
        $this->operator = $operator;
    }

    public function parseIt($lhs, $rhs): bool
    {
        return $this->parse($lhs, $rhs);
    }
}