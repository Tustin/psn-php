<?php
namespace Tustin\PlayStation\Iterator;

use GuzzleHttp\Client;
use Tustin\PlayStation;
use InvalidArgumentException;
use Tustin\PlayStation\Factory\StoreFactory;
use Tustin\PlayStation\Model\User;
use Tustin\PlayStation\Factory\UsersFactory;
use Tustin\PlayStation\Model\Store\Concept;

class StoreSearchIterator extends AbstractApiIterator
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
     * @var int
     */
    protected $limit = 20; // Default limit previously set at 'pageSize'

    public function __construct(StoreFactory $storeFactory, string $query, string $languageCode = 'en', string $countryCode = 'us', int $limit = 20)
    {
        if (empty($query))
        {
            throw new InvalidArgumentException('[query] must contain a value.');
        }

        parent::__construct($storeFactory->getHttpClient());
        $this->limit = $limit;
        $this->query = $query;
        $this->languageCode = $languageCode;
        $this->countryCode = $countryCode;
        $this->access('');
    }

    public function access($cursor): void
    {
        $results = $this->postJson('search/v1/universalSearch', [
            'age' => '69',
            'countryCode' => $this->countryCode,
            'domainRequests' => [
                [
                    'domain' => 'ConceptGameMobileApp',
                    'pagination' => [
                        'cursor' => $cursor,
                        'pageSize' => $this->limit // @TODO: Test if this can be altered.
                    ]
                ]
            ],
            'languageCode' => $this->languageCode,
            'searchTerm' => $this->query
        ]);

        $this->update($results->domainResponses[0]->totalResultCount, $results->domainResponses[0]->results, $results->domainResponses[0]->next);
    }

    public function next(): void
    {
        $this->currentOffset++;
        if (($this->currentOffset % $this->limit) === 0)
        {
            $this->access($this->customCursor);
        }
    }

    public function current()
    {
        $concept = $this->getFromOffset($this->currentOffset)->conceptProductMetadata;

        return Concept::fromObject($this->getHttpClient(), $concept);
    }
}