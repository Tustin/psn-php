<?php
namespace Tustin\PlayStation\Factory;

use Iterator;
use IteratorAggregate;
use Tustin\PlayStation\Model\Trophy\Trophy;
use Tustin\PlayStation\Iterator\TrophyIterator;
use Tustin\PlayStation\Model\Trophy\TrophyGroup;

class TrophyFactory implements IteratorAggregate
{
    private array $platforms = [];

    private string $withName = '';
    private string $withDetail = '';

    public function __construct(private TrophyGroup $group)
    {
    }

    /**
     * Gets the iterator and applies any filters.
     *
     * @return Iterator
     */
    public function getIterator(): Iterator
    {
        $iterator = new TrophyIterator($this->group);

        return $iterator;
    }

    /**
     * Gets the first trophy title in the collection.
     *
     * @return Trophy
     */
    public function first(): Trophy
    {
        return $this->getIterator()->current();
    }
}