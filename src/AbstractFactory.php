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

use Psr\Container\ContainerInterface;

/**
 * Class AbstractFactory
 *
 * @package CoiSA\Factory
 */
final class AbstractFactory implements StaticFactoryInterface
{
    /**
     * @var null|ContainerInterface
     */
    private static $container;

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
     * @throws \ReflectionException
     */
    public static function create()
    {
        $class = \func_get_arg(0);

        if (null !== self::$container && self::$container->has($class)) {
            return new ContainerFactory(self::$container, $class);
        }

        try {
            return new StaticFactoryProxyFactory($class);
        } catch (\UnexpectedValueException $unexpectedValueException) {
            return new ReflectionFactory($class);
        }
    }

    /**
     * @param ContainerInterface $container
     */
    public static function setContainer(ContainerInterface $container)
    {
        self::$container = $container;
    }
}
