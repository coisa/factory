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

use CoiSA\Factory\Exception\ReflectionException;

/**
 * Class AbstractFactoryFactory.
 *
 * @package CoiSA\Factory
 */
final class AbstractFactoryFactory implements FactoryInterface
{
    /**
     * @var string
     */
    private $abstractFactory;

    /**
     * AbstractFactoryFactory constructor.
     *
     * @param string $abstractFactory
     *
     * @throws \UnexpectedValueException
     */
    public function __construct($abstractFactory)
    {
        if (false === \class_exists($abstractFactory)) {
            throw ReflectionException::forClassNotFound($abstractFactory);
        }

        $implements               = \class_implements($abstractFactory);
        $abstractFactoryInterface = 'CoiSA\\Factory\\AbstractFactoryInterface';

        if (false === \in_array($abstractFactoryInterface, $implements)) {
            throw ReflectionException::forClassNotSubclassOf($abstractFactory, $abstractFactoryInterface);
        }

        $this->abstractFactory = $abstractFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $callable  = array($this->abstractFactory, 'create');
        $arguments = \func_get_args();

        return \call_user_func_array($callable, $arguments);
    }
}
