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
     *
     * @return object
     * @throws \ReflectionException
     */
    private static function getFactory($className)
    {
        if (!isset(self::$instances[$className])) {
            self::$instances[$className] = new ReflectionFactory($className);
        }

        return self::$instances[$className];
    }

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
}
