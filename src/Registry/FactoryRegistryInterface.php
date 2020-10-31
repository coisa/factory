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
 * Interface FactoryRegistryInterface.
 *
 * @package CoiSA\Factory\FactoryRegistry
 */
interface FactoryRegistryInterface
{
    /**
     * @param string           $class
     * @param FactoryInterface $factory
     *
     * @return void
     */
    public static function set($class, FactoryInterface $factory);

    /**
     * @param string $class
     *
     * @return bool
     */
    public static function has($class);

    /**
     * @param string $class
     *
     * @throws OutOfBoundsException
     *
     * @return FactoryInterface
     */
    public static function get($class);
}
