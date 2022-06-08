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

use CoiSA\Factory\Exception\OutOfBoundsException;
use CoiSA\Factory\Exception\ReflectionException;
use CoiSA\Factory\FactoryInterface;

/**
 * Interface FactoryRegistryInterface.
 *
 * @package CoiSA\Factory\FactoryRegistry
 */
interface FactoryRegistryInterface
{
    /**
     * @param FactoryInterface|string $factory
     *
     * @throws ReflectionException
     */
    public static function set(string $class, $factory): void;

    public static function has(string $class): bool;

    /**
     * @throws OutOfBoundsException
     */
    public static function get(string $class): FactoryInterface;
}
