<?php

namespace Tustin\PlayStation;

use GuzzleHttp\Client;
use Tustin\PlayStation\Api;
use Tustin\PlayStation\Interfaces\FactoryInterface;

class Model
{
    /**
     * The cache for the model.
     */
    private array $cache = [];

    public function __construct(array $data = [])
    {
        $this->setCache($data);
    }

    public function &__get(string $key): mixed
    {
        if (!array_key_exists($key, $this->cache)) {
            throw new \InvalidArgumentException('Key [' . $key . '] does not exist on model [' . get_class($this) . ']');
        }

        return $this->cache[$key];
    }

    /**
     * Sets the cache property.
     */
    public function setCache(array $data): void
    {
        $this->cache = $data;
    }

    /**
     * Gets the current model's cache.
     */
    public function getCache(): array
    {
        return $this->cache;
    }
}
