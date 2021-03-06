<?php

/**
 * This file is part of coisa/factory.
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * @link      https://github.com/coisa/factory
 *
 * @copyright Copyright (c) 2020 Felipe Sayão Lobato Abreu <github@felipeabreu.com.br>
 * @license   https://opensource.org/licenses/MIT MIT License
 */
namespace CoiSA\Factory;

use CoiSA\Factory\Exception\ArgumentCountError;
use Psr\Container\ContainerInterface;

/**
 * Class AbstractFactory.
 *
 * @package CoiSA\Factory
 */
final class AbstractFactory implements AbstractFactoryInterface
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
     * {@inheritdoc}
     *
     * @param string $className
     * @param mixed  $arguments [optional] Zero or more arguments to be passed to factory create method
     *
     * @throws ArgumentCountError
     */
    public static function create()
    {
        if (\func_num_args() === 0) {
            throw ArgumentCountError::forExpectedAtLeast(1);
        }

        $arguments = \func_get_args();
        $className = \array_shift($arguments);

        $factory = self::getFactory($className);

        return \call_user_func_array(array($factory, 'create'), $arguments);
    }

    /**
     * @param ContainerInterface $container
     */
    public static function setContainer(ContainerInterface $container)
    {
        FactoryAbstractFactory::setContainer($container);
    }

    /**
     * @param string                  $class
     * @param FactoryInterface|string $factory
     *
     * @return void
     */
    public static function setFactory($class, $factory)
    {
        FactoryAbstractFactory::setFactory($class, $factory);
    }

    /**
     * @param string $class
     *
     * @return FactoryInterface
     */
    public static function getFactory($class)
    {
        return FactoryAbstractFactory::getFactory($class);
    }
}
