<?php

namespace Tustin\PlayStation\Iterator;

use Tustin\PlayStation\Model\Trophy\UserTrophyTitle;
use Tustin\PlayStation\Model\Trophy\UserTrophyGroup;

class UserTrophyGroupsIterator extends AbstractApiIterator
{
    public function __construct(private UserTrophyTitle $title)
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
            'trophy/v1/users/' . $this->title->user()->accountId() . '/npCommunicationIds/' . $this->title->npCommunicationId() . '/trophyGroups',
            [
                'npServiceName' => $this->title->serviceName()
            ]
        );

        $this->update(count($results->trophyGroups), $results->trophyGroups);
    }

    /**
     * Gets the current trophy group in the iterator.
     */
    public function current(): UserTrophyGroup
    {
        $group = new UserTrophyGroup(
            $this->title,
            $this->getFromOffset($this->currentOffset)->trophyGroupId,
        );

        return $group->withUser($this->title->user());
    }
}
