<?php

namespace Tustin\PlayStation\Search;

use Tustin\PlayStation\Client;
use Tustin\PlayStation\Interfaces\SearchRequest;

class UserSearchRequest implements SearchRequest
{
    public function __construct(
        public string $query,
        public int $limit = 50,
        public string $offset = '',
        public string $languageCode = 'en',
        public string $countryCode = 'us',
        public int $age = 99,
    ) {
    }

    /**
     * Get the search URI.
     */
    public static function getSearchUri(): string
    {
        return '/api/search/v1/universalSearch';
    }

    /**
     * Set the offset for the search request.
     */
    public function setOffset(mixed $offset): void
    {
        $this->offset = $offset;
    }

    /**
     * Set the limit for the search request.
     */
    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    /**
     * Determine if the search request should use a custom cursor.
     */
    public function useCustomCursor(): bool
    {
        return true;
    }

    /**
     * Get the search parameters as an array.
     */
    public function toArray(): array
    {
        return [
            'age' => (string) $this->age,
            'countryCode' => $this->countryCode,
            'domainRequests' => [
                [
                    'domain' => 'SocialAllAccounts',
                    'pagination' => [
                        'cursor' => (string) $this->offset,
                        'pageSize' => (string) $this->limit
                    ]
                ]
            ],
            'languageCode' => $this->languageCode,
            'searchTerm' => $this->query
        ];
    }
}
