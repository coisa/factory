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
 * Class AbstractFactoryTest.
 *
 * @package CoiSA\Factory
 */
final class AbstractFactoryTest extends TestCase implements AbstractFactoryInterface
{
    private $container;

    public function setUp()
    {
        $this->container = $this->prophesize('Psr\\Container\\ContainerInterface');
    }

    public function testCreateWithContainerWillReturnContainerFactory()
    {
        $this->markTestIncomplete();

        $class = \uniqid('class', false);

        $object        = new \stdClass();
        $object->class = $class;

        $this->container->has($class)->willReturn(true);
        $this->container->get($class)->willReturn($object);

        AbstractFactory::setContainer($this->container->reveal());

        $containerFactory = AbstractFactory::create($class);

        self::assertInstanceOf('CoiSA\\Factory\\ContainerFactory', $containerFactory);
        self::assertSame($object, $containerFactory->create());
    }

    public function testGetFactoryWithStringFactoryGivenWillReturnFactoryInstance()
    {
        $objectClass  = \uniqid('class', false);
        $factoryClass = 'CoiSA\\Factory\\Stub\\Factory\\TestFactory';

        AbstractFactory::setFactory($objectClass, $factoryClass);

        $factory = AbstractFactory::getFactory($objectClass);

        self::assertInstanceOf($factoryClass, $factory);
    }

    /**
     * @expectedException \ArgumentCountError
     */
    public function testCreateWithoutArgumentsWillThrowArgumentCountErrorException()
    {
        AbstractFactory::create();
    }

    /**
     * @expectedException \ReflectionException
     */
    public function testCreateWithNonExistentClassWillThrowException()
    {
        AbstractFactory::create(__NAMESPACE__ . '\\' . \uniqid('Test', false));
    }

    public function testCreateWithoutArgumentsReturnObjectOnClassWithoutConstructor()
    {
        $object = AbstractFactory::create(__NAMESPACE__ . '\\Stub\\ClassWithoutConstructor');

        self::assertInstanceOf(__NAMESPACE__ . '\\Stub\\ClassWithoutConstructor', $object);
    }

    public function testCreateWithoutArgumentsReturnInitializedObjectOnClassWithoutArgumentConstructor()
    {
        $object = AbstractFactory::create(__NAMESPACE__ . '\\Stub\\ConstructorWithoutArgument');

        self::assertInstanceOf(__NAMESPACE__ . '\\Stub\\ConstructorWithoutArgument', $object);
        self::assertStringStartsWith('test', $object->argument);
    }

    public static function create()
    {
        $object       = new \stdClass();
        $object->argv = \func_get_args();

        return $object;
    }

    public function testCreateWithAbstractFactoryImplementationWillReturnCreateFromGivenClass()
    {
        $class = \get_called_class();
        $arg1  = \uniqid('arg1', true);
        $arg2  = \uniqid('arg2', true);

        $object = AbstractFactory::create($class, $arg1, $arg2);

        self::assertInstanceOf('stdClass', $object);
        self::assertEquals(array($arg1, $arg2), $object->argv);
    }

    public function testGetFactoryWillReturnGivenSetFactory()
    {
        $class   = \get_called_class();
        $factory = new CallableFactory(function() {
            return true;
        });

        AbstractFactory::setFactory($class, $factory);

        self::assertSame($factory, AbstractFactory::getFactory($class));
    }
}
