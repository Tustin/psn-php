<?php

namespace Tustin\PlayStation\Iterators;

use Tustin\PlayStation\Models\Group;
use Tustin\PlayStation\Factories\Groups;
use Tustin\PlayStation\Factories\GroupsFactory;

class GroupsIterator extends AbstractApiIterator
{
    public function __construct(int $limit = 50, private bool $favorited = false)
    {
        $this->limit = $limit;

        $this->access(0);
    }

    /**
     * Accesses a new page of results.
     */
    public function access(mixed $cursor): void
    {
        $results = $this->get('gamingLoungeGroups/v1/members/me/groups', [
            'favoriteFilter' => $this->favorited ? 'favorite' : 'notFavorite',
            'limit' => $this->limit,
            'offset' => $cursor,
            'includeFields' => 'groupName,groupIcon,members,mainThread,joinedTimestamp,modifiedTimestamp,totalGroupCount,isFavorite,existsNewArrival,partySessions'
        ]);

        $this->update($results->totalGroupCount, $results->groups);
    }

    /**
     * Gets the current group in the iterator.
     */
    public function current(): Group
    {
        $data = $this->getFromOffset($this->currentOffset);

        return Group::fromObject(
            $data->groupId,
            $data
        );
    }
}
