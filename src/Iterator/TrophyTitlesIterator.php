<?php
namespace Tustin\PlayStation\Iterator;

use Tustin\PlayStation\Factory\TrophyTitlesFactory;
use Tustin\PlayStation\Model\Trophy\UserTrophyTitle;

class TrophyTitlesIterator extends AbstractApiIterator
{
    // private $platforms;

    private TrophyTitlesFactory $trophyTitlesFactory;
    
    public function __construct(TrophyTitlesFactory $trophyTitlesFactory)
    {
        parent::__construct($trophyTitlesFactory->getHttpClient());

        $this->trophyTitlesFactory = $trophyTitlesFactory;
        
        // $this->platforms = implode(',', $trophyTitlesFactory->getPlatforms());

        $this->limit = 100;
        
        $this->access(0);
    }

    public function access(mixed $cursor): void
    {
        $body = [
            'limit' => $this->limit,
            'offset' => $cursor,
        ];

        $results = $this->get('trophy/v1/users/' . $this->trophyTitlesFactory->getUser()->accountId() .'/trophyTitles', $body);

        $this->update($results->totalItemCount, $results->trophyTitles);
    }

    public function current(): UserTrophyTitle
    {
		$title = new UserTrophyTitle($this->trophyTitlesFactory->getHttpClient());
		$title->setFactory($this->trophyTitlesFactory);
		$title->setCache($this->getFromOffset($this->currentOffset));

		return $title;
    }
}
