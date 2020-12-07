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
 * Class ReflectionClassFactory.
 *
 * @package CoiSA\Factory
 */
final class ReflectionClassFactory implements FactoryInterface
{
    /**
     * @var \ReflectionClass
     */
    private $reflectionClass;

    /**
     * ReflectionClassFactory constructor.
     *
     * @param mixed $objectOrClassName
     *
     * @throws \ReflectionException
     */
    public function __construct($objectOrClassName)
    {
        try {
            $this->reflectionClass = new \ReflectionClass($objectOrClassName);
        } catch (\ReflectionException $reflectionException) {
            throw ReflectionException::create(
                $reflectionException->getMessage(),
                $reflectionException->getCode(),
                $reflectionException
            );
        }

        if (false === $this->reflectionClass->isInstantiable()) {
            throw ReflectionException::forUninstantiableClass($this->reflectionClass->getName());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        if (null === $this->reflectionClass->getConstructor()
            && \method_exists($this->reflectionClass, 'newInstanceWithoutConstructor')
        ) {
            return $this->reflectionClass->newInstanceWithoutConstructor();
        }

        if (\func_num_args() === 0) {
            return $this->reflectionClass->newInstance();
        }

        return $this->reflectionClass->newInstanceArgs(\func_get_args());
    }
}
