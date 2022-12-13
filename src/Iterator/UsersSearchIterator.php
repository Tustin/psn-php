<?php

namespace Tustin\PlayStation\Iterator;

use Tustin\PlayStation\Model\User;
use Tustin\PlayStation\Factory\UsersFactory;

class UsersSearchIterator extends AbstractApiIterator
{
    public function __construct(private UsersFactory $usersFactory, private string $query, private string $languageCode = 'en', private string $countryCode = 'us')
    {
        if (empty($query)) {
            throw new \InvalidArgumentException('[query] must contain a value.');
        }

        parent::__construct($usersFactory->getHttpClient());
        $this->limit = 50;
        $this->access('');
    }

    /**
     * Accesses a new page of results.
     */
    public function access(mixed $cursor): void
    {
        // @TODO: Since the search function seems to be streamlined now, we could probably throw this into the abstract api iterator??
        $results = $this->postJson('search/v1/universalSearch', [
            'age' => '69',
            'countryCode' => $this->countryCode,
            'domainRequests' => [
                [
                    'domain' => 'SocialAllAccounts',
                    'pagination' => [
                        'cursor' => $cursor,
                        'pageSize' => '50' // 50 is max.
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
    public function current(): User
    {
        $socialMetadata = $this->getFromOffset($this->currentOffset)->socialMetadata;
        //$token = $this->getFromOffset($this->currentOffset)->id; // Do we need this??

        return User::fromObject(
            $this->usersFactory->getHttpClient(),
            $socialMetadata
        )->setCountry($socialMetadata->country);
    }
}
