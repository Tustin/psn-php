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

    public function __construct(public ?string $id = null)
    {
    }

    /**
     * Constructs a new model from an array of data.
     */
    public static function constructFrom(array $data = []): static
    {
        $object = new static($data['id'] ?? null);

        $object->setCache($data);

        return $object;
    }

    public function __isset($name)
    {
        return isset($this->cache[$name]);
    }

    public function __unset($name)
    {
        unset($this->cache[$name]);
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
