<?php

namespace Tustin\PlayStation\Iterator;

use Tustin\PlayStation\Model\GameTitle;
use Tustin\PlayStation\Factory\GameListFactory;

class GameListIterator extends AbstractApiIterator
{
    public function __construct(private GameListFactory $gameListFactory)
    {
        parent::__construct($gameListFactory->getHttpClient());

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

        $results = $this->get('gamelist/v2/users/' . $this->gameListFactory->getUser()->accountId() . '/titles', $body);

        $this->update($results->totalItemCount, $results->titles);
    }

    /**
     * Gets the current game title in the iterator.
     */
    public function current(): GameTitle
    {
        return GameTitle::fromObject(
            $this->gameListFactory,
            $this->getFromOffset($this->currentOffset)
        );
    }
}
