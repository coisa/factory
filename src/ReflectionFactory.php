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
 * Class ReflectionFactory
 *
 * @package CoiSA\Factory
 */
final class ReflectionFactory implements FactoryInterface
{
    /**
     * @var \ReflectionClass
     */
    private $reflectionClass;

    /**
     * ReflectionFactory constructor.
     *
     * @param mixed $objectOrClassName
     *
     * @throws \ReflectionException
     */
    public function __construct($objectOrClassName)
    {
        $this->reflectionClass = new \ReflectionClass($objectOrClassName);
    }

    /**
     * {@inheritDoc}
     */
    public function create()
    {
        if (\func_num_args() === 0) {
            return $this->reflectionClass->newInstance();
        }

        return $this->reflectionClass->newInstanceArgs(\func_get_args());
    }
}
