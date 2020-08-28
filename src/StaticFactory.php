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
    public static function create($className)
    {
        $factory = self::getFactory($className);

        $arguments = func_get_args();
        array_shift($arguments);

        return call_user_func_array(array($factory, 'create'), $arguments);
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
    public static function getFactory($className)
    {
        if (!isset(self::$factories[$className])) {
            self::setFactory($className, new ReflectionFactory($className));
        }

        return self::$factories[$className];
    }
}
