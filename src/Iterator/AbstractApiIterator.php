<?php

namespace Tustin\PlayStation\Iterator;

use Iterator;
use Countable;
use RuntimeException;
use Tustin\PlayStation\Api;
use InvalidArgumentException;

abstract class AbstractApiIterator extends Api implements Iterator, Countable
{
    /**
     * @var integer
     */
    protected $currentOffset = 0;

    /**
     * @var integer|null
     */
    protected $limit = null;

    /**
     * @var integer
     */
    protected $totalResults = 0;

    /**
     * @var bool
     */
    protected $lastBlock = false;

    /**
     * @var mixed
     */
    protected $customCursor = null;

    /**
     * @var bool|null
     */
    protected $force = null;

    /**
     * The cache of all items for a given iterator.
     *
     * @var array
     */
    protected $cache = [];

    /**
     * Access a specific cursor in the API.
     * 
     * @param mixed $cursor
     * @return void
     */
    public abstract function access($cursor): void;

    /**
     * Currents the current offset.
     *
     * @return integer|string
     */
    public function key()
    {
        return $this->currentOffset;
    }

    /**
     * Gets the item count.
     *
     * @return integer
     */
    public final function count(): int
    {
        return $this->getTotalResults();
    }

    /**
     * Gets the total results.
     *
     * @return integer
     */
    public function getTotalResults(): int
    {
        return $this->totalResults;
    }

    /**
     * Sets the total results.
     *
     * @param integer $results
     * @return void
     */
    protected final function setTotalResults(int $results): void
    {
        $this->totalResults = $results;
    }

    /**
     * Checks if the current offset exists in the cache.
     *
     * @return bool
     */
    public final function valid(): bool
    {
        return array_key_exists($this->currentOffset, $this->cache);
    }

    /**
     * Resets the iterator to the first item.
     *
     * @return void
     */
    public function rewind()
    {
        $this->currentOffset = 0;
    }

    /**
     * Updates the total result count and adds the new items onto the cache.
     *
     * @param integer $totalResults
     * @param array $items
     * @return void
     */
    public final function update(int $totalResults, array $items, $customCursor = null)
    {
        $this->setTotalResults($totalResults);

        $this->cache = array_merge($this->cache, $items);

        $this->customCursor = $customCursor;
    }

    /**
     * Set whether to force the iterator to keep accessing values or not.
     *
     * @param boolean $value
     * @return void
     */
    public function force(bool $value)
    {
        $this->force = $value;
    }

    /**
     * Points the offset to the next item.
     * 
     * Will request data from the API whenever necessary.
     *
     * @return void
     */
    public function next(): void
    {
        $this->currentOffset++;

        if (is_null($this->limit)) {
            return;
        }

        if ($this->currentOffset % $this->limit === 0 && $this->currentOffset < $this->getTotalResults()) {
            $this->access($this->currentOffset);
        }
    }


    /**
     * Gets an item from cache, or from the API resource if necessary, by an offset.
     *
     * @param integer|string $offset
     * @return object
     */
    public function getFromOffset($offset): object
    {
        if (is_null($offset)) {
            throw new InvalidArgumentException("Offset cannot be null.");
        }

        if (!$this->offsetExists($offset)) {
            throw new InvalidArgumentException("Offset $offset does not exist.");
        }

        if (!array_key_exists($offset, $this->cache)) {
            $this->access($offset);
        }

        return $this->cache[$offset];
    }

    /**
     * Ensures that an offset exists before trying to access it by an offset.
     *
     * @param integer|string $offset
     * @return boolean
     */
    public function offsetExists($offset): bool
    {
        try {
            return $offset >= 0 && $offset < $this->getTotalResults();
        } catch (RuntimeException $e) {
            return !$this->lastBlock;
        }
    }

    /**
     * Appends a new collection of items onto the cache.
     *
     * @param array $items
     * @return void
     */
    protected final function appendToCache(array $items)
    {
        $this->cache = array_merge($this->cache, $items);
    }

    public function first()
    {
        $this->rewind();

        return $this->current();
    }
}
