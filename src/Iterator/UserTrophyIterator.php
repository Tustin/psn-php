<?php

namespace Tustin\PlayStation\Iterator;

use Tustin\PlayStation\Model\Trophy\UserTrophy;
use Tustin\PlayStation\Model\Trophy\UserTrophyGroup;

class UserTrophyIterator extends AbstractApiIterator
{
    public function __construct(private UserTrophyGroup $trophyGroup)
    {
        parent::__construct($trophyGroup->title()->getHttpClient());

        $this->access(0);
    }

    /**
     * Accesses a new page of results.
     */
    public function access(mixed $cursor): void
    {
        $results = $this->get(
            'trophy/v1/users/' . $this->trophyGroup->title()->user()->accountId() . '/npCommunicationIds/' . $this->trophyGroup->title()->npCommunicationId() . '/trophyGroups/' . $this->trophyGroup->id() . '/trophies',
            [
                'npServiceName' => $this->trophyGroup->title()->serviceName()
            ]
        );

        $this->update($results->totalItemCount, $results->trophies);
    }

    /**
     * Gets the current trophy in the iterator. 
     */
    public function current(): UserTrophy
    {
        return UserTrophy::fromObject(
            $this->trophyGroup,
            $this->getFromOffset($this->currentOffset),
        );
    }
}
