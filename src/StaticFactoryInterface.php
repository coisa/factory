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
 * Interface StaticFactoryInterface
 *
 * @package CoiSA\Factory
 */
interface StaticFactoryInterface
{
    /**
     * @param string  $className
     * @param mixed[] $arguments
     *
     * @return object
     */
    public static function newInstance($className, array $arguments = null);

    /**
     * @param string  $className
     * @param mixed[] $arguments
     *
     * @return object
     */
    public static function getInstance($className, array $arguments = null);
}
