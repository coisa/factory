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
 * Class StaticFactoryTest.
 *
 * @package CoiSA\Factory
 */
final class StaticFactoryTest extends TestCase implements StaticFactoryInterface
{
    public function testClassIsFinal()
    {
        $reflectionClass = new \ReflectionClass('CoiSA\\Factory\\StaticFactory');

        self::assertTrue($reflectionClass->isFinal());
    }

    /**
     * @expectedException \ArgumentCountError
     */
    public function testCreateWithoutArgumentsWillThrowBadMethodCallException()
    {
        StaticFactory::create();
    }

    /**
     * @expectedException \ReflectionException
     */
    public function testCreateWithNonExistentClassWillThrowException()
    {
        StaticFactory::create(__NAMESPACE__ . '\\' . \uniqid('Test', false));
    }

    public function testCreateWithoutArgumentsReturnObjectOnClassWithoutConstructor()
    {
        $object = StaticFactory::create(__NAMESPACE__ . '\\Stub\\ClassWithoutConstructor');

        self::assertInstanceOf(__NAMESPACE__ . '\\Stub\\ClassWithoutConstructor', $object);
    }

    public function testCreateWithoutArgumentsReturnInitializedObjectOnClassWithoutArgumentConstructor()
    {
        $object = StaticFactory::create(__NAMESPACE__ . '\\Stub\\ConstructorWithoutArgument');

        self::assertInstanceOf(__NAMESPACE__ . '\\Stub\\ConstructorWithoutArgument', $object);
        self::assertStringStartsWith('test', $object->argument);
    }

    public static function create()
    {
        $object       = new \stdClass();
        $object->argv = \func_get_args();

        return $object;
    }

    public function testCreateWithStaticFactoryImplementationWillReturnCreateFromGivenClass()
    {
        $class = \get_called_class();
        $arg1  = \uniqid('arg1', true);
        $arg2  = \uniqid('arg2', true);

        $object = StaticFactory::create($class, $arg1, $arg2);

        self::assertInstanceOf('stdClass', $object);
        self::assertEquals(array($arg1, $arg2), $object->argv);
    }

    public function testGetFactoryWillReturnGivenSetFactory()
    {
        $class   = \get_called_class();
        $factory = new CallableFactory(function() {
            return true;
        });

        StaticFactory::setFactory($class, $factory);

        self::assertSame($factory, StaticFactory::getFactory($class));
    }
}
