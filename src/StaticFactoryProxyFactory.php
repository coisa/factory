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
 * Class StaticFactoryProxyFactory.
 *
 * @package CoiSA\Factory
 */
final class StaticFactoryProxyFactory implements FactoryInterface
{
    /**
     * @var string
     */
    private $staticFactory;

    /**
     * StaticFactoryProxyFactory constructor.
     *
     * @param string $staticFactory
     *
     * @throws \UnexpectedValueException
     */
    public function __construct($staticFactory)
    {
        if (false === \class_exists($staticFactory)) {
            throw ReflectionException::forClassNotFound($staticFactory);
        }

        $implements             = \class_implements($staticFactory);
        $staticFactoryInterface = 'CoiSA\\Factory\\StaticFactoryInterface';

        if (false === \in_array($staticFactoryInterface, $implements)) {
            throw ReflectionException::forClassNotSubclassOf($staticFactory, $staticFactoryInterface);
        }

        $this->staticFactory = $staticFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $callable  = array($this->staticFactory, 'create');
        $arguments = \func_get_args();

        return \call_user_func_array($callable, $arguments);
    }
}
