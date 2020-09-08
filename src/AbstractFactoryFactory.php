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
 * Class AbstractFactoryFactory
 *
 * @package CoiSA\Factory
 */
class AbstractFactoryFactory implements FactoryFactoryInterface
{
    /**
     * @param string $class
     *
     * @return FactoryInterface
     */
    public function __invoke($class)
    {
        return $this->factory($class);
    }

    /**
     * {@inheritDoc}
     */
    public function factory($class)
    {
        try {
            return new StaticFactoryProxyFactory($class);
        } catch (\UnexpectedValueException $unexpectedValueException) {
            return new ReflectionFactory($class);
        }
    }
}
