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

use CoiSA\Factory\Exception\InvalidArgumentException;

/**
 * Class CallableFactoryFactory
 *
 * @package CoiSA\Factory
 */
final class CallableFactoryFactory extends AbstractFactoryFactory
{
    /**
     * @var callable
     */
    private $callable;

    /**
     * CallableFactoryFactory constructor.
     *
     * @param $callable
     */
    public function __construct($callable)
    {
        if (false === \is_callable($callable)) {
            throw InvalidArgumentException::isNotCallable('callable');
        }

        $this->callable = $callable;
    }

    /**
     * {@inheritDoc}
     */
    public function create()
    {
        $class    = \func_get_arg(0);
        $callable = $this->callable;
        $closure  = function ($arguments) use ($class, $callable) {
            return $callable($class, $arguments);
        };

        return new CallableFactory($closure);
    }
}
