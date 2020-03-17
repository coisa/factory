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
    private static $factory;

    /**
     * @return FactoryInterface
     */
    private static function getFactory()
    {
        if (!isset(self::$factory)) {
            self::$factory = new ReflectionFactory();
        }

        return self::$factory;
    }

    /**
     * @param string $className
     * @param array $arguments optional
     *
     * @return object
     */
    public static function newInstance($className, array $arguments = null)
    {
        return self::getFactory()->newInstance($className, $arguments);
    }

    /**
     * @param string $className
     * @param array|null $arguments
     *
     * @return object
     */
    public static function getInstance($className, array $arguments = null)
    {
        return self::getFactory()->getInstance($className, $arguments);
    }
}
