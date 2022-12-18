<?php
namespace Tustin\PlayStation\Factory;

use Tustin\PlayStation\Api;
use Tustin\PlayStation\Enum\TrophyType;
use Tustin\PlayStation\Interfaces\FactoryInterface;
use Tustin\PlayStation\Iterator\TrophyGroupsIterator;
use Tustin\PlayStation\Model\Trophy\AbstractTrophyTitle;
use Tustin\PlayStation\Iterator\Filter\TrophyGroup\NameFilter;
use Tustin\PlayStation\Iterator\Filter\TrophyGroup\DetailFilter;
use Tustin\PlayStation\Iterator\Filter\TrophyGroup\TrophyTypeFilter;
use Tustin\PlayStation\Model\Trophy\AbstractTrophyGroup;

class TrophyGroupsFactory extends Api implements \IteratorAggregate, FactoryInterface
{
    private string $withName = '';
    private string $withDetail = '';

    private array $certainTrophyTypeFilter = [];

    public function __construct(private AbstractTrophyTitle $title)
    {
    }

    public function withName(string $name)
    {
        $this->withName = $name;

        return $this;
    }

    public function withDetail(string $detail)
    {
        $this->withDetail = $detail;
        
        return $this;
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
        $iterator = new TrophyGroupsIterator($this->title);

        if ($this->withName)
        {
            $iterator = new NameFilter($iterator, $this->withName);
        }

        if ($this->withDetail)
        {
            $iterator = new DetailFilter($iterator, $this->withDetail);
        }

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
    public function first(): AbstractTrophyGroup
    {
        return $this->getIterator()->current();
    }
}