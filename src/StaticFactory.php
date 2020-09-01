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
     * Prevent class from being initialized.
     */
    private function __construct()
    {
    }

    /**
     * {@inheritDoc}
     *
     * @param string $className
     * @param mixed  $arguments [optional] Zero or more arguments to be passed to the construct the object
     *
     * @throws \BadMethodCallException
     * @throws \ReflectionException
     */
    public static function create()
    {
        if (\func_num_args() === 0) {
            throw new \BadMethodCallException('You should inform at least one argument to create an instance');
        }

        $arguments = \func_get_args();
        $className = \array_shift($arguments);

        $factory = self::getFactory($className);

        return \call_user_func_array(array($factory, 'create'), $arguments);
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
        if (isset(self::$factories[$className])) {
            return self::$factories[$className];
        }

        try {
            self::setFactory($className, new StaticFactoryFactory($className));
        } catch (\UnexpectedValueException $unexpectedValueException) {
            self::setFactory($className, new ReflectionFactory($className));
        }

        return self::$factories[$className];
    }
}
