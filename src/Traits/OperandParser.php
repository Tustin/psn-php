<?php
namespace Tustin\PlayStation\Traits;

use RuntimeException;

trait OperandParser
{
    /**
     * The operator being used.
     */
    protected string $operator;

    /**
     * Parses a custom operator value.
     * @throws RuntimeException
     */
    protected function parse(mixed $lhs, mixed $rhs): bool
    {
        if (!$this->operator)
        {
            throw new RuntimeException('No such property [operator] exists on class [' . get_class($this) . '], which uses OperandParser.');
        }

        if (!is_string($this->operator))
        {
            throw new RuntimeException("Operator value [$this->operator] is not a string.");
        }

        switch ($this->operator)
        {
            case '=':
            case '==':
            case '===':
            return $lhs === $rhs;
            case '>':
            return $lhs > $rhs;
            case '>=':
            return $lhs >= $rhs;
            case '<':
            return $lhs < $rhs;
            case '<=':
            return $lhs <= $rhs;
            case '!=':
            case '!=':
            case '<>':
            return $lhs !== $rhs;
            default:
            throw new RuntimeException("Operator value [$this->operator] is not supported.");
        }

    }
}