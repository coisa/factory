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

use CoiSA\Factory\Exception\InvalidArgumentException;
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
        $factoryInterface = 'CoiSA\\Factory\\FactoryInterface';

        if (\is_string($factory)) {
            if (false === \class_exists($factory)) {
                throw ReflectionException::forClassNotFound($factory);
            }

            $reflectionClass = new \ReflectionClass($factory);

            if (false === $reflectionClass->implementsInterface($factoryInterface)) {
                throw ReflectionException::forClassNotSubclassOf($factory, $factoryInterface);
            }
        } else if (!$factory instanceof FactoryInterface) {
            throw InvalidArgumentException::forInvalidArgumentType('factory', $factoryInterface . '|string');
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
