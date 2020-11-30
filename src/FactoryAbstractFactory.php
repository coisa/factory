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

use CoiSA\Factory\Registry\FactoryRegistry;
use Psr\Container\ContainerInterface;

/**
 * Class FactoryAbstractFactory.
 *
 * @package CoiSA\Factory
 */
final class FactoryAbstractFactory implements AbstractFactoryInterface
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
     * {@inheritdoc}
     */
    public static function create()
    {
        $class          = \func_get_arg(0);
        $factoryFactory = new FactoryFactory(self::$container);

        return $factoryFactory->create($class);
    }

    /**
     * @param ContainerInterface $container
     */
    public static function setContainer(ContainerInterface $container)
    {
        self::$container = $container;
    }

    /**
     * @param string                  $class
     * @param FactoryInterface|string $factory
     *
     * @return void
     */
    public static function setFactory($class, $factory)
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
        if (FactoryRegistry::has($class)) {
            return FactoryRegistry::get($class);
        }

        return self::create($class);
    }
}
