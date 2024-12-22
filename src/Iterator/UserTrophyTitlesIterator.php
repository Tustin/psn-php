<?php

namespace Tustin\PlayStation\Iterator;

use Tustin\PlayStation\Factory\UserTrophyTitles;
use Tustin\PlayStation\Model\Trophy\UserTrophyTitle;

class UserTrophyTitlesIterator extends AbstractApiIterator
{
    public function __construct(private UserTrophyTitles $userTrophyTitles, int $limit = 100)
    {
        parent::__construct($userTrophyTitles->getHttpClient());

        $this->limit = $limit;

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

        $results = $this->get('trophy/v1/users/' . $this->userTrophyTitles->getUser()->accountId() . '/trophyTitles', $body);

        $this->update($results->totalItemCount, $results->trophyTitles);
    }

    /**
     * Gets the current user trophy title in the iterator. 
     */
    public function current(): UserTrophyTitle
    {
        return (new UserTrophyTitle)->setCache($this->getFromOffset($this->currentOffset));
    }
}
