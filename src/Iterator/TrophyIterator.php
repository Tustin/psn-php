<?php
namespace Tustin\PlayStation\Iterator;

use Tustin\PlayStation\Model\Trophy;
use Tustin\PlayStation\Model\TrophyGroup;
use Tustin\PlayStation\Model\UserTrophyTitle;

class TrophyIterator extends AbstractApiIterator
{
    /**
     * Current trophy title.
     *
     * @var TrophyGroup
     */
    private $trophyGroup;
    
    public function __construct(TrophyGroup $trophyGroup)
    {
        parent::__construct($trophyGroup->title()->getHttpClient());

        $this->trophyGroup = $trophyGroup;

        $this->access(0);
    }

    public function access($cursor) : void
    {
        $results = $this->get(
            'trophy/v1/npCommunicationIds/' . $this->trophyGroup->title()->npCommunicationId()  . '/trophyGroups/'  . $this->trophyGroup->id() . '/trophies',
            [
                'npServiceName' => 'trophy',
                'offset' => $cursor
            ]
        );
        
        $this->update($results->totalItemCount, $results->trophies);
    }

    public function current()
    {
        return Trophy::fromObject(
            $this->trophyGroup,
            $this->getFromOffset($this->currentOffset),
        );
    }
}
