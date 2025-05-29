<?php

namespace Tustin\PlayStation\Iterators;

use Tustin\PlayStation\Models\User;
use Tustin\PlayStation\Factories\Users;

class UsersSearchIterator extends AbstractApiIterator
{
    public function __construct(private string $query, int $limit = 50, private string $languageCode = 'en', private string $countryCode = 'us')
    {
        parent::__construct();

        $this->limit = $limit;

        $this->access('');
    }

    /**
     * Accesses a new page of results.
     */
    public function access(mixed $cursor): void
    {
        $results = $this->postJson('search/v1/universalSearch', [
            'age' => '69',
            'countryCode' => $this->countryCode,
            'domainRequests' => [
                [
                    'domain' => 'SocialAllAccounts',
                    'pagination' => [
                        'cursor' => $cursor,
                        'pageSize' => $this->limit
                    ]
                ]
            ],
            'languageCode' => $this->languageCode,
            'searchTerm' => $this->query
        ]);

        $domainResponse = $results->domainResponses[0];

        $this->update($domainResponse->totalResultCount, $domainResponse->results, $domainResponse->next ?? "");
    }

    /**
     * Gets the current user in the iterator.
     */
    public function current(): ?User
    {
        $socialMetadata = $this->getFromOffset($this->currentOffset)?->socialMetadata;

        if (!$socialMetadata) {
            return null;
        }

        return User::fromObject(
            $socialMetadata
        )->setCountry($socialMetadata->country);
    }
}
