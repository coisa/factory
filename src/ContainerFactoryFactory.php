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

use Psr\Container\ContainerInterface;

/**
 * Class ContainerFactoryFactory
 *
 * @package CoiSA\Factory
 */
final class ContainerFactoryFactory extends AbstractFactoryFactory
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * ContainerFactoryFactory constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Psr\Container\ContainerExceptionInterface error while retrieving the entry
     */
    public function factory($class)
    {
        if (false === $this->container->has($class)) {
            return parent::factory($class);
        }

        return new ContainerFactory($this->container, $class);
    }
}
