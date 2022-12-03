<?php
namespace Tustin\PlayStation\Iterator;

use Tustin\PlayStation\Model\GameTitle;
use Tustin\PlayStation\Factory\GameListFactory;

class GameListIterator extends AbstractApiIterator
{
    private $gameListFactory;
    
    public function __construct(GameListFactory $gameListFactory)
    {
        parent::__construct($gameListFactory->getHttpClient());

        $this->gameListFactory = $gameListFactory;

        $this->limit = 100;
        
        $this->access(0);
    }

    public function access(mixed $cursor): void
    {
        $body = [
            'limit' => $this->limit,
            'offset' => $cursor,
        ];

        $results = $this->get('gamelist/v2/users/' . $this->gameListFactory->getUser()->accountId() .'/titles', $body);

        $this->update($results->totalItemCount, $results->titles);
    }

    public function current(): GameTitle
    {
        return GameTitle::fromObject(
            $this->gameListFactory,
            $this->getFromOffset($this->currentOffset)
        );
    }
}
