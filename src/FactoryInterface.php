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
 * Interface FactoryInterface
 *
 * @package CoiSA\Factory
 */
interface FactoryInterface
{
    /**
     * Create new instance with given arguments.
     *
     * @param mixed $arguments [optional] Zero or more arguments to be passed to construct the object
     *
     * @return object
     */
    public function create();
}
