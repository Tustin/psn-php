<?php

namespace Tustin\PlayStation\Iterator;

use Tustin\PlayStation\Model\Trophy\Trophy;
use Tustin\PlayStation\Model\Trophy\TrophyGroup;
use Tustin\PlayStation\Model\Trophy\UserTrophyTitle;

class TrophyIterator extends AbstractApiIterator
{
    public function __construct(private TrophyGroup $trophyGroup)
    {
        parent::__construct($trophyGroup->title()->getHttpClient());

        $this->access(0);
    }

    /**
     * Accesses a new page of results.
     */
    public function access(mixed $cursor): void
    {
        if ($this->trophyGroup->title() instanceof UserTrophyTitle) {
            $results = $this->get(
                'trophy/v1/users/' . $this->trophyGroup->title()->getFactory()->getUser()->accountId() . '/npCommunicationIds/' . $this->trophyGroup->title()->npCommunicationId() . '/trophyGroups/' . $this->trophyGroup->id() . '/trophies',
                [
                    'npServiceName' => $this->trophyGroup->title()->serviceName()
                ]
            );
        } else {
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

    /**
     * Gets the current trophy in the iterator. 
     */
    public function current(): Trophy
    {
        return Trophy::fromObject(
            $this->trophyGroup,
            $this->getFromOffset($this->currentOffset),
        );
    }
}
