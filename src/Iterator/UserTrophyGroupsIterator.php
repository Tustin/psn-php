<?php

namespace Tustin\PlayStation\Iterator;

use Tustin\PlayStation\Model\User;
use Tustin\PlayStation\Traits\HasUser;
use Tustin\PlayStation\Enum\TrophyType;
use Tustin\PlayStation\Model\Trophy\UserTrophyGroup;
use Tustin\PlayStation\Model\Trophy\UserTrophyTitle;
use Tustin\PlayStation\Iterator\Filter\TrophyGroup\TrophyTypeFilter;

class UserTrophyGroupsIterator extends AbstractApiIterator
{
    use HasUser;

    private array $certainTrophyTypeFilter = [];

    public function __construct(private UserTrophyTitle $title, private User $user)
    {
        parent::__construct($title->getHttpClient());

        $this->access(0);
    }

    public function withTrophyCount(TrophyType $trophy, string $operand, int $count): self
    {
        $this->certainTrophyTypeFilter[] = [$trophy, $operand, $count];

        return $this;
    }

    public function withTotalTrophyCount(string $operand, int $count): void
    {
        // 
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
     * Gets the iterator and applies any filters.
     */
    public function getIterator(): \Iterator
    {
        $iterator = $this;

        if ($this->certainTrophyTypeFilter)
        {
            foreach ($this->certainTrophyTypeFilter as $filter)
            {
                $iterator = new TrophyTypeFilter($iterator, ...$filter);
            }
        }

        return $iterator;
    }

    /**
     * Gets the current trophy group in the iterator.
     */
    public function current(): UserTrophyGroup
    {
        $cache = $this->getFromOffset($this->currentOffset);

        $group = new UserTrophyGroup(
            $this->title,
            $this->user(),
            $cache->trophyGroupId,
        );

        return $group->hydrate($cache);
    }

    /**
     * Gets the first trophy group.
     */
    public function first(): UserTrophyGroup
    {
        return $this->getIterator()->current();
    }
}
