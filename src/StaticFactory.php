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

namespace CoiSA\Factory;

/**
 * Class StaticFactory
 *
 * @package CoiSA\Factory
 */
final class StaticFactory implements StaticFactoryInterface
{
    /**
     * @var FactoryInterface[]
     */
    private static $factories;

    /**
     * {@inheritDoc}
     *
     * @throws \ReflectionException
     */
    public static function newInstance($className, array $arguments = null)
    {
        return self::getFactory($className)->newInstance($arguments);
    }

    /**
     * {@inheritDoc}
     *
     * @throws \ReflectionException
     */
    public static function getInstance($className, array $arguments = null)
    {
        return self::getFactory($className)->getInstance($arguments);
    }

    /**
     * @param string           $className
     * @param FactoryInterface $factory
     *
     * @return void
     */
    public static function setFactory($className, FactoryInterface $factory)
    {
        self::$factories[$className] = $factory;
    }

    /**
     * @param string $className
     *
     * @throws \ReflectionException
     *
     * @return FactoryInterface
     */
    private static function getFactory($className)
    {
        if (!isset(self::$factories[$className])) {
            self::setFactory($className, new ReflectionFactory($className));
        }

        return self::$factories[$className];
    }
}
