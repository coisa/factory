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

namespace CoiSA\Factory;

/**
 * Interface FactoryFactoryInterface
 *
 * @package CoiSA\Factory
 */
interface FactoryFactoryInterface
{
    /**
     * Create new factory instance with given class.
     *
     * @param string $class class name to create a factory object
     *
     * @return FactoryInterface
     */
    public function createFactory($class);
}
