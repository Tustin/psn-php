<?php
namespace Tustin\PlayStation\Iterator;

use Tustin\PlayStation\Model\Trophy\Trophy;
use Tustin\PlayStation\Model\Trophy\TrophyGroup;
use Tustin\PlayStation\Model\Trophy\UserTrophyTitle;


class TrophyIterator extends AbstractApiIterator
{
    /**
     * Current trophy title.
     */
    private TrophyGroup $trophyGroup;
    
    public function __construct(TrophyGroup $trophyGroup)
    {
        parent::__construct($trophyGroup->title()->getHttpClient());

        $this->trophyGroup = $trophyGroup;

        $this->access(0);
    }

    public function access(mixed $cursor): void
    {
        if ($this->trophyGroup->title() instanceof UserTrophyTitle)
        {
            $results = $this->get(
                'trophy/v1/users/' . $this->trophyGroup->title()->getFactory()->getUser()->accountId() . '/npCommunicationIds/' . $this->trophyGroup->title()->npCommunicationId() .'/trophyGroups/' . $this->trophyGroup->id() . '/trophies',
                [
                    'npServiceName' => $this->trophyGroup->title()->serviceName()
                ]
            );
        }
        else
        {
            $results = $this->get(
                'trophy/v1/npCommunicationIds/' . $this->trophyGroup->title()->npCommunicationId() . '/trophyGroups/' . $this->trophyGroup->id() . '/trophies',
                [
                    'npServiceName' => $this->trophyGroup->title()->serviceName(),
                    'offset' => $cursor
                ]
            );
        }
        
        $this->update($results->totalItemCount, $results->trophies);
    }

    public function current(): Trophy
    {
        return Trophy::fromObject(
            $this->trophyGroup,
            $this->getFromOffset($this->currentOffset),
        );
    }
}
