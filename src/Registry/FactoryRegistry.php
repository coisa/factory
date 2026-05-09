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

namespace CoiSA\Factory\Registry;

use CoiSA\Factory\AbstractFactoryFactory;
use CoiSA\Factory\AbstractFactoryInterface;
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
     * @var array<FactoryInterface|string>
     */
    private static $factories = [];

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
    public static function set(string $class, $factory): void
    {
        if (false === \is_string($factory) && !$factory instanceof FactoryInterface) {
            throw InvalidArgumentException::forInvalidArgumentType('factory', FactoryInterface::class . '|string');
        }

        if (\is_string($factory) && false === class_exists($factory)) {
            throw ReflectionException::forClassNotFound($factory);
        }

        $givenFactoryReflectionClass = new \ReflectionClass($factory);

        if (false === $givenFactoryReflectionClass->implementsInterface(FactoryInterface::class)
            && false === $givenFactoryReflectionClass->implementsInterface(AbstractFactoryInterface::class)
        ) {
            throw ReflectionException::forClassNotSubclassOf($factory, FactoryInterface::class);
        }

        self::$factories[$class] = $factory;
    }

    /**
     * {@inheritdoc}
     */
    public static function has(string $class): bool
    {
        return \array_key_exists($class, self::$factories);
    }

    /**
     * {@inheritdoc}
     */
    public static function get(string $class): FactoryInterface
    {
        if (false === self::has($class)) {
            throw OutOfBoundsException::forNotFoundClassFactory($class);
        }

        $factory = self::$factories[$class];

        if (false === \is_string($factory)) {
            return $factory;
        }

        $reflectionClass = new \ReflectionClass($factory);

        if ($reflectionClass->implementsInterface(AbstractFactoryInterface::class)) {
            return new AbstractFactoryFactory($factory);
        }

        return $reflectionClass->newInstance();
    }
}
