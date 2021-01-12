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
    public function pluck(string $property, bool $ignoreCache = false)
    {
        if (!$this->hasCached() || $ignoreCache)
        {
            if (!(new ReflectionClass($this))->implementsInterface(Fetchable::class))
            {
				return null;
                // throw new RuntimeException('Model [' . get_class($this) . '] has not been cached, 
                // but doesn\'t implement Fetchable to make requests.');
            }

            $this->setCache($this->fetch());
            $this->pluck($property);
        }
        
        if (empty($this->cache))
        {
            throw new InvalidArgumentException('Failed to populate cache for model [' . get_class($this) . ']');
        }

        $pieces = explode('.', $property);

        $root = $pieces[0];

        if (!array_key_exists($root, $this->cache))
        {
            return null;
            // throw new InvalidArgumentException("[$root] is not a valid property for model [" . get_class($this) . "]");
        }

        $value = $this->cache[$root];

        array_shift($pieces);

        foreach ($pieces as $piece)
        {
            if (!is_array($value))
            {
                throw new RuntimeException("Value [$value] passed to pluck is not an array, but tried accessing a key from it.");
            }
            
            $value = $value[$piece];
        }

        return $value;
    }

    protected function hasCached() : bool
    {
        return isset($this->cache) && !empty($this->cache);
    }

    public function setCache($data)
    {
        // So this is bad and probably slow, but it's less annoying than some recursive method.
        $this->cache = json_decode(json_encode($data, JSON_FORCE_OBJECT), true);
    }

    public function getFactory() : FactoryInterface
    {
        return $this->factory;
    }

    public function setFactory(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }
}