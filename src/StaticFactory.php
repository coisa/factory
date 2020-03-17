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
    private static $factories;

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
        self::$factories[$className] = $factory;
    }

    /**
     * @param string $className
     *
     * @return object
     * @throws \ReflectionException
     */
    private static function getFactory($className)
    {
        if (!isset(self::$factories[$className])) {
            self::setFactory($className, new ReflectionFactory($className));
        }

        return self::$factories[$className];
    }
}
