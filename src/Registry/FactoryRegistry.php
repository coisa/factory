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
namespace CoiSA\Factory\Registry;

use CoiSA\Factory\Exception\OutOfBoundsException;
use CoiSA\Factory\FactoryInterface;

/**
 * Class FactoryRegistry.
 *
 * @package CoiSA\Factory\FactoryRegistry
 */
final class FactoryRegistry implements FactoryRegistryInterface
{
    /**
     * @var FactoryInterface[]
     */
    private static $factories = array();

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
    public static function set($class, FactoryInterface $factory)
    {
        self::$factories[$class] = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public static function has($class)
    {
        return \array_key_exists($class, self::$factories);
    }

    /**
     * {@inheritdoc}
     */
    public static function get($class)
    {
        if (false === self::has($class)) {
            throw OutOfBoundsException::forNotFoundClassFactory($class);
        }

        return self::$factories[$class];
    }
}
