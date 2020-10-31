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

/**
 * Interface AbstractFactoryInterface.
 *
 * @package CoiSA\Factory
 */
interface AbstractFactoryInterface
{
    /**
     * Create new instance of a class with given arguments.
     *
     * @throws \UnexpectedValueException when given arguments are invalid for create a new instance
     *
     * @return object
     */
    public static function create();
}
