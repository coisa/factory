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

use Doctrine\Common\Annotations\AnnotationReader;
use Psr\Container\ContainerInterface;

/**
 * Class FactoryFactory.
 *
 * @package CoiSA\Factory
 */
final class FactoryFactory implements FactoryInterface
{
    private ?ContainerInterface $container;

    /**
     * FactoryFactory constructor.
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
     */
    public function create()
    {
        $class = func_get_arg(0);

        if (null !== $this->container && $this->container->has($class)) {
            return new ContainerFactory($this->container, $class);
        }

        if (\PHP_VERSION_ID >= 80000) {
            try {
                return new ReflectionAttributeFactory($class);
            } catch (\Throwable $throwable) {
            }
        }

        if (class_exists(AnnotationReader::class)) {
            try {
                return new DoctrineAnnotationFactory($class);
            } catch (\Throwable $throwable) {
            }
        }

        try {
            return new AbstractFactoryFactory($class);
        } catch (\Throwable $throwable) {
            return new ReflectionClassFactory($class);
        }
    }
}
