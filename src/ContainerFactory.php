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
 * Class ContainerFactory
 *
 * @package CoiSA\Factory
 */
final class ContainerFactory implements FactoryInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string
     */
    private $class;

    /**
     * ContainerFactory constructor.
     *
     * @param ContainerInterface $container
     * @param string             $class
     */
    public function __construct(ContainerInterface $container, $class)
    {
        $this->container = $container;
        $this->class     = $class;
    }

    /**
     * {@inheritDoc}
     *
     * @throws \Psr\Container\NotFoundExceptionInterface  no entry was found for **this** identifier
     * @throws \Psr\Container\ContainerExceptionInterface error while retrieving the entry
     */
    public function create()
    {
        return $this->container->get($this->class);
    }
}
