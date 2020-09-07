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

use CoiSA\Factory\AbstractFactoryFactory;
use CoiSA\Factory\FactoryFactoryInterface;
use CoiSA\Factory\FactoryInterface;

/**
 * Class FactoryRegistry
 *
 * @package CoiSA\Factory\FactoryRegistry
 */
final class FactoryRegistry implements RegistryInterface
{
    /**
     * @var FactoryInterface[]
     */
    private static $factories = array();

    /**
     * @var FactoryFactoryInterface
     */
    private $factoryFactory;

    /**
     * FactoryRegistry constructor.
     *
     * @param null|FactoryFactoryInterface $factoryFactory
     */
    public function __construct(FactoryFactoryInterface $factoryFactory = null)
    {
        $this->factoryFactory = $factoryFactory ?: new AbstractFactoryFactory();
    }

    /**
     * @param FactoryInterface $factory
     * @param mixed            $class
     */
    public function set($class, FactoryInterface $factory)
    {
        self::$factories[$class] = $factory;
    }

    /**
     * @param string $className
     * @param mixed  $class
     *
     * @throws \ReflectionException
     *
     * @return FactoryInterface
     */
    public function get($class)
    {
        if (isset(self::$factories[$class])) {
            return self::$factories[$class];
        }

        $factory = $this->factoryFactory->create($class);
        $this->set($class, $factory);

        return $factory;
    }
}
