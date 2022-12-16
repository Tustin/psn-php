<?php

namespace Tustin\PlayStation\Iterator;

use Tustin\PlayStation\Model\Trophy\TrophyGroup;
use Tustin\PlayStation\Model\Trophy\UserTrophyTitle;
use Tustin\PlayStation\Model\Trophy\TrophyTitle;

class TrophyGroupsIterator extends AbstractApiIterator
{
    public function __construct(private TrophyTitle $title)
    {
        parent::__construct($title->getHttpClient());

        $this->access(0);
    }

    /**
     * Accesses a new page of results.
     */
    public function access(mixed $cursor): void
    {
        $results = $this->get(
            'trophy/v1/npCommunicationIds/' . $this->title->npCommunicationId() . '/trophyGroups',
            [
                'npServiceName' => $this->title->serviceName()
            ]
        );

        $this->update(count($results->trophyGroups), $results->trophyGroups);
    }

    /**
     * Gets the current trophy group in the iterator.
     */
    public function current(): TrophyGroup
    {
        return TrophyGroup::fromObject($this->title, $this->getFromOffset($this->currentOffset));
    }
}
