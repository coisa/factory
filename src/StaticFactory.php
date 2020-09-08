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

use CoiSA\Factory\Exception\BadMethodCallException;
use CoiSA\Factory\Registry\FactoryRegistry;

/**
 * Class StaticFactory
 *
 * @package CoiSA\Factory
 */
final class StaticFactory implements StaticFactoryInterface
{
    // @codeCoverageIgnoreStart

    /**
     * Prevent class from being initialized.
     */
    private function __construct()
    {
    }

    // @codeCoverageIgnoreEnd

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
            throw BadMethodCallException::forEmptyGivenArguments();
        }

        $arguments = \func_get_args();
        $className = \array_shift($arguments);

        $factory = self::getFactory($className);

        return \call_user_func_array(array($factory, 'create'), $arguments);
    }

    /**
     * @param string           $class
     * @param FactoryInterface $factory
     *
     * @return void
     */
    public static function setFactory($class, FactoryInterface $factory)
    {
        FactoryRegistry::set($class, $factory);
    }

    /**
     * @param string $class
     *
     * @return FactoryInterface
     */
    public static function getFactory($class)
    {
        return AbstractFactory::create($class);
    }
}
