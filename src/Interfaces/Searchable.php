<?php

namespace Tustin\PlayStation\Interfaces;

use Iterator;
use Tustin\PlayStation\Interfaces\SearchRequest;

interface Searchable
{
    public static function getSearchUri(): string;

    public static function performSearch(SearchRequest $searchRequest): Iterator;
}
