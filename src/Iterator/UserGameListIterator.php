<?php

namespace Tustin\PlayStation\Iterator;

use Tustin\PlayStation\Model\UserGameTitle;
use Tustin\PlayStation\Factory\UserGameListFactory;

class UserGameListIterator extends AbstractApiIterator
{
    public function __construct(private UserGameListFactory $UserGameListFactory)
    {
        parent::__construct($UserGameListFactory->getHttpClient());

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

        $results = $this->get('gamelist/v2/users/' . $this->UserGameListFactory->user()->accountId() . '/titles', $body);

        $this->update($results->totalItemCount, $results->titles);
    }

    /**
     * Gets the current game title in the iterator.
     */
    public function current(): UserGameTitle
    {
        return UserGameTitle::fromObject(
            $this->UserGameListFactory->user(),
            $this->getFromOffset($this->currentOffset)
        );
    }
}
