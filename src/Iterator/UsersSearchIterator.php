<?php

namespace Tustin\PlayStation\Iterator;

use Tustin\PlayStation;
use InvalidArgumentException;
use Tustin\PlayStation\Model\User;
use Tustin\PlayStation\Factory\UsersFactory;

class UsersSearchIterator extends AbstractApiIterator
{
    /**
     * The search query.
     *
     * @var string
     */
    protected $query;

    /**
     * The language to search with.
     *
     * @var string
     */
    protected $languageCode;

    /**
     * The country code.
     *
     * @var string
     */
    protected $countryCode;

    /**
     * The users factory.
     *
     * @var UsersFactory
     */
    private $usersFactory;

    public function __construct(UsersFactory $usersFactory, string $query, string $languageCode = 'en', string $countryCode = 'us')
    {
        if (empty($query)) {
            throw new InvalidArgumentException('[query] must contain a value.');
        }

        parent::__construct($usersFactory->getHttpClient());
        $this->usersFactory = $usersFactory;
        $this->query = $query;
        $this->languageCode = $languageCode;
        $this->countryCode = $countryCode;
        $this->limit = 50;
        $this->access('');
    }

    public function access($cursor): void
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

    public function current()
    {
        $socialMetadata = $this->getFromOffset($this->currentOffset)->socialMetadata;
        //$token = $this->getFromOffset($this->currentOffset)->id; // Do we need this??

        return User::fromObject(
            $this->usersFactory->getHttpClient(),
            $socialMetadata
        );
    }
}
