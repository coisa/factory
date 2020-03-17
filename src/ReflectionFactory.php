<?php

namespace CoiSA\Factory;

/**
 * Class ReflectionFactory
 *
 * @package CoiSA\Factory
 */
final class ReflectionFactory implements FactoryInterface
{
    /**
     * @var \ReflectionClass
     */
    private $reflectionClass;

    /**
     * @var array
     */
    private static $instances = array();

    /**
     * ReflectionFactory constructor.
     *
     * @param string $className
     *
     * @throws \ReflectionException
     */
    public function __construct($className)
    {
        $this->reflectionClass = new \ReflectionClass($className);
    }

    /**
     * @param array|null $arguments
     *
     * @return object
     */
    public function newInstance(array $arguments = null)
    {
        if (!$this->reflectionClass->hasMethod('__construct')) {
            return $this->reflectionClass->newInstanceWithoutConstructor();
        }

        if (empty($arguments)) {
            return $this->reflectionClass->newInstance();
        }

        return $this->reflectionClass->newInstanceArgs($arguments);
    }

    /**
     * @param array|null $arguments
     *
     * @return object
     */
    public function getInstance(array $arguments = null)
    {
        $hash = \serialize($arguments);

        if (!isset(self::$instances[$hash])) {
            self::$instances[$hash] = $this->newInstance($arguments);
        }

        return self::$instances[$hash];
    }
}
