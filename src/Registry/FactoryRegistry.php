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
use CoiSA\Factory\Exception\ReflectionException;
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
    public static function set($class, $factory)
    {
        if (\is_string($factory) && false === \class_exists($factory)) {
            throw ReflectionException::forClassNotFound($factory);
        }

        $interface  = 'CoiSA\\Factory\\FactoryInterface';
        $implements = \class_implements($factory);

        if (false === \in_array($interface, $implements)) {
            throw ReflectionException::forClassNotSubclassOf($factory, $interface);
        }

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

        $factory = self::$factories[$class];

        if (\is_string($factory)) {
            $factory = new $factory();
        }

        return $factory;
    }
}
