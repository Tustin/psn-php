<?php

namespace Tustin\PlayStation\Iterator;

use Tustin\PlayStation\Client;
use Tustin\PlayStation\Model\GameTitle;
use Tustin\PlayStation\Model\UserGameTitle;
use Tustin\PlayStation\Factory\UserGameList;
use Tustin\PlayStation\Factory\GameListFactory;

class GameListIterator extends AbstractApiIterator
{
    public function __construct(private UserGameList $userGameList, int $limit = 100)
    {
        parent::__construct(
            Client::getInstance()->getHttpClient(),
        );

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

        $results = $this->get('gamelist/v2/users/' . $this->userGameList->getUser()->accountId() . '/titles', $body);

        $this->update($results->totalItemCount, $results->titles);
    }

    /**
     * Gets the current game title in the iterator.
     */
    public function current(): UserGameTitle
    {
        $data = $this->getFromOffset($this->currentOffset);

        return UserGameTitle::fromObject(
            $this->userGameList->getUser()->accountId(),
            $data->titleId,
            $data
        );
    }
}
