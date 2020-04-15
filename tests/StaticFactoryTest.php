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
 * Class StaticFactoryTest
 *
 * @package CoiSA\Factory
 */
final class StaticFactoryTest extends TestCase
{
    public function testNewInstanceWithNonExistentClassWillThrowException()
    {
        $this->expectException('ReflectionException');
        StaticFactory::newInstance(__NAMESPACE__ . '\\' . \uniqid('Test', false));
    }

    public function testNewInstaceWithoutArgumentsReturnObjectOnClassWithoutConstructor()
    {
        $object = StaticFactory::newInstance(__NAMESPACE__ . '\\Stub\\ClassWithoutConstructor');

        $this->assertInstanceOf(__NAMESPACE__ . '\\Stub\\ClassWithoutConstructor', $object);
    }

    public function testNewInstaceWithoutArgumentsReturnInitializedObjectOnClassWithoutArgumentConstructor()
    {
        $object = StaticFactory::newInstance(__NAMESPACE__ . '\\Stub\\ConstructorWithoutArgument');

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
        $expected = StaticFactory::getInstance($className, $arguments);

        $this->assertSame($expected, StaticFactory::getInstance($className, $arguments));
        $this->assertSame($expected, StaticFactory::getInstance($className, $arguments));
    }
}
