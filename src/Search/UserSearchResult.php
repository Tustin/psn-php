<?php

namespace Tustin\PlayStation\Search;

use Tustin\PlayStation\Interfaces\SearchResult;


class UserSearchResult implements SearchResult
{
    public function __construct(
        public array $data
    ) {
    }
}
