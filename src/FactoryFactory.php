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

use Psr\Container\ContainerInterface;

/**
 * Class FactoryFactory.
 *
 * @package CoiSA\Factory
 */
final class FactoryFactory implements FactoryInterface
{
    /**
     * @var null|ContainerInterface
     */
    private $container;

    /**
     * FactoryFactory constructor.
     *
     * @param ContainerInterface|null $container
     */
    public function __construct(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \ReflectionException
     *
     * @return FactoryInterface
     *
     * @TODO Add support to \Doctrine\Common\Annotation
     * @TODO Add support to \ReflectionAttribute (PHP 8.0)
     */
    public function create()
    {
        $class = \func_get_arg(0);

        if (null !== $this->container && $this->container->has($class)) {
            return new ContainerFactory($this->container, $class);
        }

        try {
            return new StaticFactoryProxyFactory($class);
        } catch (\Throwable $throwable) {
            return new ReflectionFactory($class);
        }
    }
}
