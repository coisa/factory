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

use PHPUnit\Framework\TestCase;

/**
 * Class ReflectionFactoryTest
 *
 * @package CoiSA\Factory
 */
final class ReflectionFactoryTest extends TestCase
{
    public function testConstructorWithNonExistentClassWillThrowException()
    {
        $className = __NAMESPACE__ . '\\' . \uniqid('Test', false);

        $this->setExpectedException('ReflectionException');
        new ReflectionFactory($className);
    }

    public function testCreateWithoutArgumentsReturnObjectOnClassWithoutConstructor()
    {
        $className         = __NAMESPACE__ . '\\Stub\\ClassWithoutConstructor';
        $reflectionFactory = new ReflectionFactory($className);

        $object = $reflectionFactory->create();

        self::assertInstanceOf(__NAMESPACE__ . '\\Stub\\ClassWithoutConstructor', $object);
    }

    public function testCreateWithoutArgumentsReturnInitializedObjectOnClassWithoutArgumentConstructor()
    {
        $className         = __NAMESPACE__ . '\\Stub\\ConstructorWithoutArgument';
        $reflectionFactory = new ReflectionFactory($className);

        $object = $reflectionFactory->create();

        self::assertInstanceOf(__NAMESPACE__ . '\\Stub\\ConstructorWithoutArgument', $object);
        self::assertStringStartsWith('test', $object->argument);
    }
}
