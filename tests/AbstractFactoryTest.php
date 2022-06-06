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

use PHPUnit\Framework\TestCase;

/**
 * Class AbstractFactoryTest.
 *
 * @package CoiSA\Factory
 *
 * @internal
 * @coversNothing
 */
final class AbstractFactoryTest extends TestCase implements AbstractFactoryInterface
{
    private $container;

    protected function setUp(): void
    {
        $this->container = $this->prophesize('Psr\\Container\\ContainerInterface');
    }

    public function testCreateWithContainerWillReturnContainerFactory(): void
    {
        parent::markTestIncomplete();

        $class = uniqid('class', false);

        $object        = new \stdClass();
        $object->class = $class;

        $this->container->has($class)->willReturn(true);
        $this->container->get($class)->willReturn($object);

        AbstractFactory::setContainer($this->container->reveal());

        $containerFactory = AbstractFactory::create($class);

        parent::assertInstanceOf('CoiSA\\Factory\\ContainerFactory', $containerFactory);
        parent::assertSame($object, $containerFactory->create());
    }

    public function testGetFactoryWithStringFactoryGivenWillReturnFactoryInstance(): void
    {
        $objectClass  = uniqid('class', false);
        $factoryClass = 'CoiSA\\Factory\\Stub\\Factory\\TestFactory';

        AbstractFactory::setFactory($objectClass, $factoryClass);

        $factory = AbstractFactory::getFactory($objectClass);

        parent::assertInstanceOf($factoryClass, $factory);
    }

    public function testCreateWithoutArgumentsWillThrowArgumentCountErrorException(): void
    {
        $this->expectException(\ArgumentCountError::class);

        AbstractFactory::create();
    }

    public function testCreateWithNonExistentClassWillThrowException(): void
    {
        $this->expectException(\ReflectionException::class);

        AbstractFactory::create(__NAMESPACE__ . '\\' . uniqid('Test', false));
    }

    public function testCreateWithoutArgumentsReturnObjectOnClassWithoutConstructor(): void
    {
        $object = AbstractFactory::create(__NAMESPACE__ . '\\Stub\\ClassWithoutConstructor');

        parent::assertInstanceOf(__NAMESPACE__ . '\\Stub\\ClassWithoutConstructor', $object);
    }

    public function testCreateWithoutArgumentsReturnInitializedObjectOnClassWithoutArgumentConstructor(): void
    {
        $object = AbstractFactory::create(__NAMESPACE__ . '\\Stub\\ConstructorWithoutArgument');

        parent::assertInstanceOf(__NAMESPACE__ . '\\Stub\\ConstructorWithoutArgument', $object);
        parent::assertStringStartsWith('test', $object->argument);
    }

    public static function create()
    {
        $object       = new \stdClass();
        $object->argv = \func_get_args();

        return $object;
    }

    public function testCreateWithAbstractFactoryImplementationWillReturnCreateFromGivenClass(): void
    {
        $class = static::class;
        $arg1  = uniqid('arg1', true);
        $arg2  = uniqid('arg2', true);

        $object = AbstractFactory::create($class, $arg1, $arg2);

        parent::assertInstanceOf('stdClass', $object);
        parent::assertSame([$arg1, $arg2], $object->argv);
    }

    public function testGetFactoryWillReturnGivenSetFactory(): void
    {
        $class   = static::class;
        $factory = new CallableFactory(fn () => true);

        AbstractFactory::setFactory($class, $factory);

        parent::assertSame($factory, AbstractFactory::getFactory($class));
    }
}
