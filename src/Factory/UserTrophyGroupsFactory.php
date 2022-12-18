<?php
namespace Tustin\PlayStation\Factory;

use Tustin\PlayStation\Api;
use Tustin\PlayStation\Enum\TrophyType;
use Tustin\PlayStation\Interfaces\FactoryInterface;
use Tustin\PlayStation\Model\Trophy\UserTrophyGroup;
use Tustin\PlayStation\Model\Trophy\UserTrophyTitle;
use Tustin\PlayStation\Iterator\UserTrophyGroupsIterator;
use Tustin\PlayStation\Iterator\Filter\TrophyGroup\TrophyTypeFilter;

class UserTrophyGroupsFactory extends Api implements \IteratorAggregate, FactoryInterface
{
    private array $certainTrophyTypeFilter = [];

    public function __construct(private UserTrophyTitle $title)
    {
    }

    public function withTrophyCount(TrophyType $trophy, string $operand, int $count)
    {
        $this->certainTrophyTypeFilter[] = [$trophy, $operand, $count];

        return $this;
    }

    public function withTotalTrophyCount(string $operand, int $count)
    {
        // 
    }

    /**
     * Gets the iterator and applies any filters.
     */
    public function getIterator(): \Iterator
    {
        $iterator = new UserTrophyGroupsIterator($this->title);

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
     * Gets the first trophy group.
     */
    public function first(): UserTrophyGroup
    {
        return $this->getIterator()->current();
    }
}