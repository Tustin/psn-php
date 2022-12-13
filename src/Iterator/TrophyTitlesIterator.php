<?php

namespace Tustin\PlayStation\Iterator;

use Tustin\PlayStation\Factory\TrophyTitlesFactory;
use Tustin\PlayStation\Model\Trophy\UserTrophyTitle;

class TrophyTitlesIterator extends AbstractApiIterator
{
    // private $platforms;

    public function __construct(private TrophyTitlesFactory $trophyTitlesFactory)
    {
        parent::__construct($trophyTitlesFactory->getHttpClient());

        // $this->platforms = implode(',', $trophyTitlesFactory->getPlatforms());

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

        $results = $this->get('trophy/v1/users/' . $this->trophyTitlesFactory->getUser()->accountId() . '/trophyTitles', $body);

        $this->update($results->totalItemCount, $results->trophyTitles);
    }

    /**
     * Gets the current user trophy title in the iterator. 
     */
    public function current(): UserTrophyTitle
    {
        $title = new UserTrophyTitle($this->trophyTitlesFactory->getHttpClient());
        $title->setFactory($this->trophyTitlesFactory);
        $title->setCache($this->getFromOffset($this->currentOffset));

        return $title;
    }
}
