<?php

namespace Tustin\PlayStation\Traits;

use ReflectionClass;
use RuntimeException;
use InvalidArgumentException;
use Tustin\PlayStation\Interfaces\Fetchable;
use Tustin\PlayStation\Interfaces\FactoryInterface;

trait Model
{
    /**
     * The cache for the model.
     *
     * @var array
     */
    private $cache = [];

    /**
     * The factory the model was instantiated by.
     *
     * @var FactoryInterface
     */
    private $factory;

    /**
     * Plucks an API property from the cache. Will populate cache if necessary.
     *
     * @suppress PhanUndeclaredMethod
     * @param string $property
     * @param bool $ignoreCache
     * @return mixed
     */
    public function pluck($property, $ignoreCache = false)
    {
        if (!$this->hasCached() || $ignoreCache) {
            if (!(new ReflectionClass($this))->implementsInterface(Fetchable::class)) {
                return null;
            }

            $this->setCache($this->fetch());
            $this->pluck($property);
        }

        if (empty($this->cache)) {
            throw new InvalidArgumentException('Failed to populate cache for model [' . get_class($this) . ']');
        }

        $pieces = explode('.', $property);

        $root = $pieces[0];

        if (!array_key_exists($root, $this->cache)) {
            return null;
        }

        $value = $this->cache[$root];

        array_shift($pieces);

        foreach ($pieces as $piece) {
            if (!is_array($value)) {
                throw new RuntimeException("Value [$value] passed to pluck is not an array, but tried accessing a key from it.");
            }

            $value = $value[$piece];
        }

        return $value;
    }

    /**
     * Checks if the cache has been set.
     *
     * @return boolean
     */
    protected function hasCached()
    {
        return isset($this->cache) && !empty($this->cache);
    }

    /**
     * Sets the cache property.
     *
     * @param object $data
     * @return void
     */
    public function setCache($data)
    {
        // So this is bad and probably slow, but it's less annoying than some recursive method.
        $this->cache = json_decode(json_encode($data, JSON_FORCE_OBJECT), true);
    }

    /**
     * Gets the factory for this model.
     *
     * @return FactoryInterface
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Sets the factory for this model
     *
     * @param FactoryInterface $factory
     * @return void
     */
    public function setFactory($factory)
    {
        $this->factory = $factory;
    }
}
