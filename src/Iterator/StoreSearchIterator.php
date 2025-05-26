<?php

namespace Tustin\PlayStation\Iterator;

use Tustin\PlayStation\Model\Store\Concept;

class StoreSearchIterator extends AbstractApiIterator
{
    public function __construct(
        private string $query,
        int $limit = 20,
        private string $languageCode = 'en',
        private string $countryCode = 'us'
    ) {

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
                    'domain' => 'ConceptGameMobileApp', // TODO: Need to find the new domain for this search type (throws an error now)
                    'pagination' => [
                        'cursor' => $cursor,
                        'pageSize' => $this->limit
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
        $concept = $this->getFromOffset($this->currentOffset);

        dd($concept);

        return Concept::fromObject(
            $this->getFromOffset($this->currentOffset)->conceptProductMetadata
        );
    }
}
