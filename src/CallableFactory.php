<?php

namespace CoiSA\Factory;

/**
 * Class CallableFactory
 *
 * @package CoiSA\Factory
 */
final class CallableFactory implements FactoryInterface
{
    /**
     * @var callable
     */
    private $callable;

    /**
     * @var array
     */
    private static $instances = array();

    /**
     * CallableFactory constructor.
     *
     * @param $className
     *
     * @throws \ReflectionException
     */
    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    /**
     * @param array|null $arguments
     *
     * @return object
     */
    public function newInstance(array $arguments = null)
    {
        if (empty($arguments)) {
            return call_user_func($this->callable);
        }

        return call_user_func_array($this->callable, $arguments);
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
