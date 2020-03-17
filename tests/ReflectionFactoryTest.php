<?php

namespace CoiSA\Factory;

use PHPUnit\Framework\TestCase;
use CoiSA\Factory\Stub\ConstructorWithoutArgument;

/**
 * Class ReflectionFactoryTest
 *
 * @package CoiSA\Factory
 */
final class ReflectionFactoryTest extends TestCase
{
    /**
     * @var ReflectionFactory
     */
    private $reflectionFactory;

    public function setUp()
    {
        $this->reflectionFactory = new ReflectionFactory();
    }

    public function test_new_instance_with_non_existent_class_will_throw_exception()
    {
        $this->setExpectedException('ReflectionException');
        $this->reflectionFactory->newInstance(__NAMESPACE__ . '\\' . \uniqid('Test', false));
    }

    public function test_new_instace_without_arguments_return_object_on_class_without_constructor()
    {
        $object = $this->reflectionFactory->newInstance(__NAMESPACE__ . '\\Stub\\ClassWithoutConstructor');

        $this->assertInstanceOf(__NAMESPACE__ . '\\Stub\\ClassWithoutConstructor', $object);
    }

    public function test_new_instace_without_arguments_return_initialized_object_on_class_without_argument_constructor()
    {
        $object = $this->reflectionFactory->newInstance(__NAMESPACE__ . '\\Stub\\ConstructorWithoutArgument');

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
     */
    public function test_get_shared_without_argument_will_return_same_instance($className, array $arguments = null)
    {
        $expected = $this->reflectionFactory->getInstance($className, $arguments);

        $this->assertSame($expected, $this->reflectionFactory->getInstance($className, $arguments));
        $this->assertSame($expected, $this->reflectionFactory->getInstance($className, $arguments));

        $sharedFactory = new ReflectionFactory();

        $this->assertSame($expected, $sharedFactory->getInstance($className, $arguments));
        $this->assertSame($expected, $sharedFactory->getInstance($className, $arguments));
    }
}
