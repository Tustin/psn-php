<?php

namespace Tustin\PlayStation\Interfaces;

use Iterator;

interface SearchRequest
{
    public function toArray(): array;

    public static function getSearchUri(): string;

    public function setLimit(int $limit): void;

    public function setOffset(mixed $offset): void;

    public function useCustomCursor(): bool;
}
