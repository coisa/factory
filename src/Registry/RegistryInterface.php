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

namespace CoiSA\Factory\Registry;

use CoiSA\Factory\FactoryInterface;

/**
 * Interface RegistryInterface
 *
 * @package CoiSA\Factory\FactoryRegistry
 */
interface RegistryInterface
{
    /**
     * @param string           $class
     * @param FactoryInterface $factory
     *
     * @return void
     */
    public function set($class, FactoryInterface $factory);

    /**
     * @param string $class
     *
     * @return FactoryInterface
     */
    public function get($class);
}
