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

use CoiSA\Factory\Registry\FactoryRegistry;
use Psr\Container\ContainerInterface;

/**
 * Class FactoryAbstractFactory.
 *
 * @package CoiSA\Factory
 */
final class FactoryAbstractFactory implements AbstractFactoryInterface
{
    private static ContainerInterface $container;

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
     */
    public static function create()
    {
        $class          = func_get_arg(0);
        $factoryFactory = new FactoryFactory(static::$container ?? null);

        return $factoryFactory->create($class);
    }

    public static function setContainer(ContainerInterface $container): void
    {
        self::$container = $container;
    }

    /**
     * @param FactoryInterface|string $factory
     */
    public static function setFactory(string $class, $factory): void
    {
        FactoryRegistry::set($class, $factory);
    }

    public static function getFactory(string $class): FactoryInterface
    {
        if (FactoryRegistry::has($class)) {
            return FactoryRegistry::get($class);
        }

        return self::create($class);
    }
}
