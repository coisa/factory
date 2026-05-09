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

use CoiSA\Factory\Attribute\Factory;
use CoiSA\Factory\Exception\ReflectionException;

/**
 * Class ReflectionAttributeFactory.
 *
 * @package CoiSA\Factory
 */
final class ReflectionAttributeFactory implements FactoryInterface
{
    private FactoryInterface $factory;

    /**
     * DoctrineAnnotationFactory constructor.
     *
     * @param object|string $objectOrClassName
     *
     * @throws ReflectionException
     */
    public function __construct($objectOrClassName)
    {
        if (\PHP_VERSION_ID < 80000) {
            throw ReflectionException::forClassNotFound('ReflectionAttribute');
        }

        try {
            $reflectionClass = new \ReflectionClass($objectOrClassName);
        } catch (\ReflectionException $reflectionException) {
            throw ReflectionException::createFromThrowable($reflectionException);
        }

        $reflectionAttributes = $reflectionClass->getAttributes(
            Factory::class,
            \ReflectionAttribute::IS_INSTANCEOF
        );

        if (empty($reflectionAttributes)) {
            throw ReflectionException::forClassAttributeNotFound(Factory::class);
        }

        $factoryAttribute = $reflectionAttributes[0]->newInstance();
        $this->factory    = $factoryAttribute->getFactory();
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $arguments = \func_get_args();

        return \call_user_func_array([$this->factory, 'create'], $arguments);
    }
}
