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
    public function testNewInstanceWithNonExistentClassWillThrowException()
    {
        $className = __NAMESPACE__ . '\\' . \uniqid('Test', false);

        $this->expectException('ReflectionException');
        new ReflectionFactory($className);
    }

    public function testNewInstaceWithoutArgumentsReturnObjectOnClassWithoutConstructor()
    {
        $className         = __NAMESPACE__ . '\\Stub\\ClassWithoutConstructor';
        $reflectionFactory = new ReflectionFactory($className);

        $object = $reflectionFactory->newInstance();

        $this->assertInstanceOf(__NAMESPACE__ . '\\Stub\\ClassWithoutConstructor', $object);
    }

    public function testNewInstaceWithoutArgumentsReturnInitializedObjectOnClassWithoutArgumentConstructor()
    {
        $className         = __NAMESPACE__ . '\\Stub\\ConstructorWithoutArgument';
        $reflectionFactory = new ReflectionFactory($className);

        $object = $reflectionFactory->newInstance();

        $this->assertInstanceOf(__NAMESPACE__ . '\\Stub\\ConstructorWithoutArgument', $object);
        $this->assertStringStartsWith('test', $object->argument);
    }

    public function provideClassNameAndArguments()
    {
        return array(
            array(__NAMESPACE__ . '\\Stub\\ClassWithoutConstructor'),
            array(__NAMESPACE__ . '\\Stub\\ConstructorWithoutArgument'),
            array(__NAMESPACE__ . '\\Stub\\ConstructorWithMixedArgument', array(\uniqid('test', true))),
            array(__NAMESPACE__ . '\\Stub\\ConstructorWithTypedArgument', array(array(\uniqid('test', true)))),
        );
    }

    /**
     * @dataProvider provideClassNameAndArguments
     *
     * @param mixed $className
     */
    public function testGetSharedWithoutArgumentWillReturnSameInstance($className, array $arguments = null)
    {
        $reflectionFactory      = new ReflectionFactory($className);
        $otherReflectionFactory = new ReflectionFactory($className);

        $expected = $reflectionFactory->getInstance($arguments);

        $this->assertSame($expected, $reflectionFactory->getInstance($arguments));
        $this->assertSame($expected, $reflectionFactory->getInstance($arguments));

        $this->assertSame($expected, $otherReflectionFactory->getInstance($arguments));
        $this->assertSame($expected, $otherReflectionFactory->getInstance($arguments));
    }
}
