<?php

namespace Tustin\PlayStation;

use GuzzleHttp\Client;
use Tustin\PlayStation\Api;
use Tustin\PlayStation\Interfaces\FactoryInterface;

abstract class Model extends Api
{
    /**
     * The cache for the model.
     */
    private array $cache = [];

    /**
     * The factory the model was instantiated by.
     */
    private FactoryInterface $factory;

    /**
     * Has data been fetched from the API?
     */
    private bool $hasFetched = false;

    /**
     * Fetches the model data from the API.
     */
    abstract public function fetch(): object;

    /**
     * Performs the fetch method while flagging the model as being fetched.
     */
    private function performFetch(): object
    {
        $this->hasFetched = true;

        return $this->fetch();
    }

    public function __construct(Client $client)
    {
        parent::__construct($client);
    }

    /**
     * Plucks an API property from the cache. Will populate cache if necessary.
     */
    public function pluck(string $property, bool $ignoreCache = false): mixed
    {
        $pieces = explode('.', $property);

        $root = $pieces[0];

        $exists = array_key_exists($root, $this->cache);

        if (!$exists)
        {
            if (!$this->hasFetched() || $ignoreCache)
            {
                $this->setCache($this->performFetch());
                return $this->pluck($property);
            }
            else
            {
                return null;
            }
        }

        if (empty($this->cache)) {
            throw new \InvalidArgumentException('Failed to populate cache for model [' . get_class($this) . ']');
        }

        $value = $this->cache[$root];

        array_shift($pieces);

        foreach ($pieces as $piece) {
            if (!is_array($value)) {
                throw new \RuntimeException("Value [$value] passed to pluck is not an array, but tried accessing a key from it.");
            }

            $value = $value[$piece];
        }

        return $value;
    }

    /**
     * Checks if data has been fetched from the API.
     */
    protected function hasFetched(): bool
    {
        return $this->hasFetched;
    }

    /**
     * Sets the cache property.
     */
    public function setCache(object $data): void
    {
        // So this is bad and probably slow, but it's less annoying than some recursive method.
        $this->cache = json_decode(json_encode($data, JSON_FORCE_OBJECT), true);
    }

    /**
     * Gets the current model's cache.
     */
    public function getCache(): array
    {
        return $this->cache;
    }

    /**
     * Gets the factory for this model.
     */
    public function getFactory(): FactoryInterface
    {
        return $this->factory;
    }

    /**
     * Sets the factory for this model
     */
    public function setFactory(FactoryInterface $factory): void
    {
        $this->factory = $factory;
    }
}