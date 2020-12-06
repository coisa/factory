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

use PHPUnit\Framework\TestCase;

/**
 * Class ReflectionClassFactoryTest.
 *
 * @package CoiSA\Factory
 */
final class ReflectionClassFactoryTest extends TestCase
{
    public function testConstructorWithNonExistentClassWillThrowException()
    {
        $className = __NAMESPACE__ . '\\' . \uniqid('Test', false);

        $this->setExpectedException('CoiSA\\Factory\\Exception\\ReflectionException');
        new ReflectionClassFactory($className);
    }

    public function testCreateWithoutArgumentsReturnObjectOnClassWithoutConstructor()
    {
        $className         = __NAMESPACE__ . '\\Stub\\ClassWithoutConstructor';
        $reflectionFactory = new ReflectionClassFactory($className);

        $object = $reflectionFactory->create();

        self::assertInstanceOf(__NAMESPACE__ . '\\Stub\\ClassWithoutConstructor', $object);
    }

    public function testCreateWithoutArgumentsReturnInitializedObjectOnClassWithoutArgumentConstructor()
    {
        $className         = __NAMESPACE__ . '\\Stub\\ConstructorWithoutArgument';
        $reflectionFactory = new ReflectionClassFactory($className);

        $object = $reflectionFactory->create();

        self::assertInstanceOf(__NAMESPACE__ . '\\Stub\\ConstructorWithoutArgument', $object);
        self::assertStringStartsWith('test', $object->argument);
    }

    public function provideArguments()
    {
        return array(
            array(array(1)),
            array(array(1, 2)),
            array(array(1, 2, 3)),
        );
    }

    /**
     * @dataProvider provideArguments
     */
    public function testCreateWithArgumentsWillInstantiateConstructorWithArguments(array $arguments)
    {
        $className         = __NAMESPACE__ . '\\Stub\\ConstructorWithMixedArgument';
        $reflectionFactory = new ReflectionClassFactory($className);

        $object = $reflectionFactory->create($arguments);

        self::assertEquals($arguments, $object->getArgument());
    }
}
