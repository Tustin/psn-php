<?php

namespace Tustin\PlayStation\Iterator;

abstract class AbstractApiIterator implements \Iterator, \Countable
{
    protected int $currentOffset = 0;

    protected ?int $limit = null;

    protected int $totalResults = 0;

    protected bool $lastBlock = false;

    protected mixed $customCursor = null;

    protected bool $useCustomCursor = false;

    protected ?bool $force = null;

    /**
     * The cache of all items for a given iterator.
     */
    protected array $cache = [];

    /**
     * Access a specific cursor in the API.
     */
    public abstract function access(mixed $cursor): void;

    /**
     * Currents the current offset.
     */
    public function key(): mixed
    {
        return $this->currentOffset;
    }

    /**
     * Gets the item count.
     */
    public final function count(): int
    {
        return $this->getTotalResults();
    }

    /**
     * Gets the total results.
     */
    public function getTotalResults(): int
    {
        return $this->totalResults;
    }

    /**
     * Sets the total results.
     */
    protected final function setTotalResults(int $results): void
    {
        $this->totalResults = $results;
    }

    /**
     * Checks if the current offset exists in the cache.
     */
    public final function valid(): bool
    {
        return array_key_exists($this->currentOffset, $this->cache);
    }

    /**
     * Resets the iterator to the first item.
     */
    public function rewind(): void
    {
        $this->currentOffset = 0;
    }

    /**
     * Updates the total result count and adds the new items onto the cache.
     */
    public final function update(int $totalResults, array $items, mixed $customCursor = null): void
    {
        $this->setTotalResults($totalResults);

        $this->cache = array_merge($this->cache, $items);

        $this->limit = count($items);

        $this->customCursor = $customCursor;
    }

    /**
     * Set whether to force the iterator to keep accessing values or not.
     */
    public function force(bool $value): void
    {
        $this->force = $value;
    }

    /**
     * Set whether to use a custom cursor or not.
     */
    public function useCustomCursor(bool $value = true): void
    {
        $this->useCustomCursor = $value;
    }

    /**
     * Points the offset to the next item.
     * 
     * Will request data from the API whenever necessary.
     */
    public function next(): void
    {
        $this->currentOffset++;

        if (is_null($this->limit)) {
            return;
        }

        if ($this->currentOffset % $this->limit === 0 && $this->currentOffset < $this->getTotalResults()) {
            if ($this->useCustomCursor) {
                if ($this->customCursor === null) {
                    return;
                }

                $this->access($this->customCursor);
            } else {
                $this->access($this->currentOffset);
            }
        }
    }


    /**
     * Gets an item from cache, or from the API resource if necessary, by an offset.
     */
    public function getFromOffset(mixed $offset): array
    {
        return $this->cache[$offset];
    }

    /**
     * Ensures that an offset exists before trying to access it by an offset.
     */
    public function offsetExists(mixed $offset): bool
    {
        return $offset >= 0 && $offset < $this->getTotalResults();
    }

    /**
     * Appends a new collection of items onto the cache.
     */
    protected final function appendToCache(array $items): void
    {
        $this->cache = array_merge($this->cache, $items);
    }

    /**
     * Gets the first item in the iterator.
     */
    public function first(): object
    {
        $this->rewind();

        return $this->current();
    }
}
