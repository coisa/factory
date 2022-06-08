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

use CoiSA\Factory\Annotation\FactoryAnnotation;
use CoiSA\Factory\Exception\ReflectionException;
use Doctrine\Common\Annotations\AnnotationReader;

/**
 * Class DoctrineAnnotationFactory.
 *
 * @package CoiSA\Factory
 */
final class DoctrineAnnotationFactory implements FactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * DoctrineAnnotationFactory constructor.
     *
     * @param object|string $objectOrClassName
     *
     * @throws ReflectionException
     */
    public function __construct($objectOrClassName)
    {
        if (false === class_exists(AnnotationReader::class)) {
            throw ReflectionException::forClassNotFound(AnnotationReader::class);
        }

        try {
            $reflectionClass = new \ReflectionClass($objectOrClassName);
        } catch (\ReflectionException $reflectionException) {
            throw ReflectionException::createFromThrowable($reflectionException);
        }

        $annotationReader  = new AnnotationReader();
        $factoryAnnotation = $annotationReader->getClassAnnotation($reflectionClass, FactoryAnnotation::class);

        if (!$factoryAnnotation instanceof FactoryAnnotation) {
            throw ReflectionException::forAnnotationNotFound(FactoryAnnotation::class);
        }

        $this->factory = $factoryAnnotation->getFactory();
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
