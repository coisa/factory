<?php

namespace CoiSA\Factory;

/**
 * Class StaticFactory
 *
 * @package CoiSA\Factory
 */
final class StaticFactory implements StaticFactoryInterface
{
    /**
     * @var array
     */
    private static $instances;

    /**
     * @param string $className
     * @param array|null $arguments
     *
     * @return object
     * @throws \ReflectionException
     */
    public static function newInstance($className, array $arguments = null)
    {
        return self::getFactory($className)->newInstance($arguments);
    }

    /**
     * @param string $className
     * @param array|null $arguments
     *
     * @return object
     * @throws \ReflectionException
     */
    public static function getInstance($className, array $arguments = null)
    {
        return self::getFactory($className)->getInstance($arguments);
    }

    /**
     * @param string $className
     * @param FactoryInterface $factory
     */
    public static function setFactory($className, FactoryInterface $factory)
    {
        self::$instances[$className] = $factory;
    }

    /**
     * @param string $className
     *
     * @return object
     * @throws \ReflectionException
     */
    private static function getFactory($className)
    {
        if (!isset(self::$instances[$className])) {
            self::setFactory($className, new ReflectionFactory($className));
        }

        return self::$instances[$className];
    }
}
