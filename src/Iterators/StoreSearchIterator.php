<?php

namespace Tustin\PlayStation\Iterators;

use Tustin\PlayStation\Models\Store\Concept;

class StoreSearchIterator extends AbstractApiIterator
{
    public function __construct(
        private string $query,
        protected ?int $limit = 20,
        private string $languageCode = 'en',
        private string $countryCode = 'us'
    ) {

        parent::__construct();

        $this->access('');
    }

    /**
     * Accesses a new page of results.
     */
    public function access(mixed $cursor): void
    {
        // @TODO: I don't think this endpoint is really used anymore, and it should probably be swapped for the graphql equivalent:
        // metGetDomainSearchResults
        // But with the new domain this works for now.
        $results = $this->postJson('search/v1/universalSearch', [
            'age' => '69',
            'countryCode' => $this->countryCode,
            'domainRequests' => [
                [
                    'domain' => 'MobileGames',
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
            $this->access($this->customCursor);
        }
    }

    /**
     * Gets the current concept in the iterator.
     */
    public function current(): Concept
    {
        $concept = $this->getFromOffset($this->currentOffset);

        return Concept::fromObject(
            $concept->id,
            $this->getFromOffset($this->currentOffset)->conceptMetadata
        );
    }
}
