<?php
namespace Tustin\PlayStation\Iterator;

use PlayStation\Api\Trophy;
use Tustin\PlayStation\Model\TrophyTitle;
use Tustin\PlayStation\Factory\TrophyTitlesFactory;

class TrophyTitlesIterator extends AbstractApiIterator
{
    private $platforms;

    private $trophyTitlesFactory;
    
    public function __construct(TrophyTitlesFactory $trophyTitlesFactory)
    {
        parent::__construct($trophyTitlesFactory->getHttpClient());

        $this->trophyTitlesFactory = $trophyTitlesFactory;
        
        $this->platforms = implode(',', $trophyTitlesFactory->getPlatforms());

        $this->limit = 128;
        
        $this->access(0);
    }

    public function access($cursor) : void
    {
        $body = [
            'platform' => $this->platforms,
            'limit' => $this->limit,
            'offset' => $cursor,
        ];

        $results = $this->get('trophy/v1/users/' . $this->trophyTitlesFactory->getUser()->accountId() .'/trophyTitles', $body);

        $this->update($results->totalItemCount, $results->trophyTitles);
    }

    public function current()
    {
		$title = new TrophyTitle($this->trophyTitlesFactory->getHttpClient());
		$title->setFactory($this->trophyTitlesFactory);
		$title->setCache($this->getFromOffset($this->currentOffset));

		return $title;
    }
}
