<?php

namespace Tustin\PlayStation\Iterators;

use Tustin\PlayStation\Models\GameTitle;
use Tustin\PlayStation\Models\UserGameTitle;
use Tustin\PlayStation\Factories\UserGameList;
use Tustin\PlayStation\Factories\GameListFactory;

class GameListIterator extends AbstractApiIterator
{
    public function __construct(private string $accountId, int $limit = 100)
    {
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

        $results = $this->get('gamelist/v2/users/' . $this->accountId . '/titles', $body);

        $this->update($results->totalItemCount, $results->titles);
    }

    /**
     * Gets the current game title in the iterator.
     */
    public function current(): UserGameTitle
    {
        $data = $this->getFromOffset($this->currentOffset);

        return UserGameTitle::fromObject(
            $this->accountId,
            $data->titleId,
            $data
        );
    }
}
