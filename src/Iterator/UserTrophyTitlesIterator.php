<?php

namespace Tustin\PlayStation\Iterator;

use Tustin\PlayStation\Iterator\AbstractApiIterator;
use Tustin\PlayStation\Model\Trophy\UserTrophyTitle;
use Tustin\PlayStation\Factory\UserTrophyTitlesFactory;

class UserTrophyTitlesIterator extends AbstractApiIterator
{
    public function __construct(private UserTrophyTitlesFactory $trophyTitlesFactory)
    {
        parent::__construct($trophyTitlesFactory->getHttpClient());

        $this->limit = 100;

        $this->access(0);
    }

    /**
     * Accesses a new page of results.
     */
    public function access(mixed $cursor): void
    {
        $body = [
            'limit' => $this->limit,
            'offset' => $cursor,
        ];

        $results = $this->get('trophy/v1/users/' . $this->trophyTitlesFactory->user()->accountId() . '/trophyTitles', $body);

        $this->update($results->totalItemCount, $results->trophyTitles);
    }

    /**
     * Gets the current user trophy title in the iterator. 
     */
    public function current(): UserTrophyTitle
    {
        $title = UserTrophyTitle::fromObject(
            $this->getHttpClient(),
            $this->getFromOffset($this->currentOffset)
        );

        return $title->withUser($this->trophyTitlesFactory->user());
    }
}
