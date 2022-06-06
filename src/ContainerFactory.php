<?php

declare(strict_types=1);

/**
 * This file is part of coisa/factory.
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * @link      https://github.com/coisa/factory
 * @copyright Copyright (c) 2020-2022 Felipe Sayão Lobato Abreu <github@felipeabreu.com.br>
 * @license   https://opensource.org/licenses/MIT MIT License
 */

namespace CoiSA\Factory;

use CoiSA\Factory\Exception\ContainerException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;

/**
 * Class ContainerFactory.
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
     * @param string $class
     */
    public function __construct(ContainerInterface $container, $class)
    {
        $this->container = $container;
        $this->class     = $class;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        try {
            return $this->container->get($this->class);
        } catch (ContainerExceptionInterface $containerException) {
            throw ContainerException::forExceptionResolvingIdentifier($containerException, $this->class);
        }
    }
}
