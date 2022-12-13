<?php

namespace Tustin\PlayStation\Iterator;

use Tustin\PlayStation\Factory\StoreFactory;
use Tustin\PlayStation\Model\Store\Concept;

class StoreSearchIterator extends AbstractApiIterator
{
    public function __construct(
        StoreFactory $storeFactory,
        private string $query,
        private string $languageCode = 'en',
        private string $countryCode = 'us'
    ) {
        if (empty($query)) {
            throw new \InvalidArgumentException('[query] must contain a value.');
        }

        parent::__construct($storeFactory->getHttpClient());
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
                    'domain' => 'ConceptGameMobileApp',
                    'pagination' => [
                        'cursor' => $cursor,
                        'pageSize' => '20' // @TODO: Test if this can be altered.
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
        if (($this->currentOffset % $this->limit) == 0) {
            $this->access($this->maxEventIndexCursor);
        }
    }

    /**
     * Gets the current concept in the iterator.
     */
    public function current(): Concept
    {
        $concept = $this->getFromOffset($this->currentOffset)->conceptProductMetadata;

        return Concept::fromObject($this->getHttpClient(), $concept);
    }
}
