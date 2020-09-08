<?php

/**
 * This file is part of coisa/factory.
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * @link      https://github.com/coisa/factory
 * @copyright Copyright (c) 2020 Felipe Sayão Lobato Abreu <github@felipeabreu.com.br>
 * @license   https://opensource.org/licenses/MIT MIT License
 */

namespace CoiSA\Factory\Registry;

use CoiSA\Factory\AbstractFactoryFactory;
use CoiSA\Factory\FactoryFactoryInterface;
use CoiSA\Factory\FactoryInterface;

/**
 * Class FactoryRegistry
 *
 * @package CoiSA\Factory\FactoryRegistry
 */
final class FactoryRegistry implements FactoryRegistryInterface
{
    /**
     * @var FactoryInterface[]
     */
    private static $factories = array();

    /**
     * @var FactoryFactoryInterface
     */
    private static $factoryFactory;

    // @codeCoverageIgnoreStart

    /**
     * Prevent class from being initialized.
     */
    private function __construct()
    {
    }

    // @codeCoverageIgnoreEnd

    /**
     * @param FactoryInterface $factory
     * @param mixed            $class
     */
    public static function set($class, FactoryInterface $factory)
    {
        self::$factories[$class] = $factory;
    }

    /**
     * @param string $className
     * @param mixed  $class
     *
     * @throws \ReflectionException
     *
     * @return FactoryInterface
     */
    public static function get($class)
    {
        if (isset(self::$factories[$class])) {
            return self::$factories[$class];
        }

        return self::getFactoryFactory()->factory($class);
    }

    /**
     * @param FactoryFactoryInterface $factoryFactory
     */
    public static function setFactoryFactory(FactoryFactoryInterface $factoryFactory)
    {
        self::$factoryFactory = $factoryFactory;
    }

    /**
     * @return FactoryFactoryInterface
     */
    private static function getFactoryFactory()
    {
        if (false === isset(self::$factoryFactory)) {
            self::setFactoryFactory(new AbstractFactoryFactory());
        }

        return self::$factoryFactory;
    }
}
