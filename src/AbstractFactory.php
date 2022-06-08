<?php

declare(strict_types=1);

/**
 * This file is part of coisa/factory.
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * @link      https://github.com/coisa/factory
 * @copyright Copyright (c) 2020-2022 Felipe Sayão Lobato Abreu <github@felipeabreu.com.br>
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
    /** @codeCoverageIgnoreStart */

    /**
     * Prevent class from being initialized.
     */
    private function __construct()
    {
    }

    /** @codeCoverageIgnoreEnd */

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
        if (0 === \func_num_args()) {
            throw ArgumentCountError::forExpectedAtLeast(1);
        }

        $arguments = \func_get_args();
        $className = array_shift($arguments);

        $factory = self::getFactory($className);

        return \call_user_func_array([$factory, 'create'], $arguments);
    }

    public static function setContainer(ContainerInterface $container): void
    {
        FactoryAbstractFactory::setContainer($container);
    }

    /**
     * @param FactoryInterface|string $factory
     */
    public static function setFactory(string $class, $factory): void
    {
        FactoryAbstractFactory::setFactory($class, $factory);
    }

    public static function getFactory(string $class): FactoryInterface
    {
        return FactoryAbstractFactory::getFactory($class);
    }
}
