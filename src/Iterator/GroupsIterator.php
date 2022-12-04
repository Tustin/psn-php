<?php
namespace Tustin\PlayStation\Iterator;

use Tustin\PlayStation\Model\Group;
use Tustin\PlayStation\Factory\GroupsFactory;

class GroupsIterator extends AbstractApiIterator
{
    /**
     * The message threads repository.
     *
     * @var GroupsFactory
     */
    private $groupsFactory;
    
    public function __construct(GroupsFactory $groupsFactory)
    {
        parent::__construct($groupsFactory->getHttpClient());

        $this->groupsFactory = $groupsFactory;
        $this->limit = 20;

        $this->access(0);
    }

    public function access(mixed $cursor): void
    {
        $results = $this->get('gamingLoungeGroups/v1/members/me/groups', [
            'favoriteFilter' => $this->groupsFactory->favorited ? 'favorite' : 'notFavorite',
            'limit' => $this->limit,
            'offset' => $cursor,
            'includeFields' => 'groupName,groupIcon,members,mainThread,joinedTimestamp,modifiedTimestamp,totalGroupCount,isFavorite,existsNewArrival,partySessions'
        ]);

        $this->update($results->totalGroupCount, $results->groups);
    }

    public function current(): Group
    {
        return Group::fromObject(
            $this->groupsFactory,
            $this->getFromOffset($this->currentOffset)
        );
    }
}