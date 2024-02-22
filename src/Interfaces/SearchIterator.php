<?php

namespace Tustin\PlayStation\Interfaces;

interface SearchIterator
{
    public function current(): mixed;

    public function next(): void;

    public function key(): ?int;

    public function valid(): bool;

    public function rewind(): void;
}
