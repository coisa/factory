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
    public function test_new_instance_with_non_existent_class_will_throw_exception()
    {
        $className = __NAMESPACE__ . '\\' . \uniqid('Test', false);

        $this->setExpectedException('ReflectionException');
        $reflectionFactory = new ReflectionFactory($className);
    }

    public function test_new_instace_without_arguments_return_object_on_class_without_constructor()
    {
        $className = __NAMESPACE__ . '\\Stub\\ClassWithoutConstructor';
        $reflectionFactory = new ReflectionFactory($className);

        $object = $reflectionFactory->newInstance();

        $this->assertInstanceOf(__NAMESPACE__ . '\\Stub\\ClassWithoutConstructor', $object);
    }

    public function test_new_instace_without_arguments_return_initialized_object_on_class_without_argument_constructor()
    {
        $className = __NAMESPACE__ . '\\Stub\\ConstructorWithoutArgument';
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
     */
    public function test_get_shared_without_argument_will_return_same_instance($className, array $arguments = null)
    {
        $reflectionFactory = new ReflectionFactory($className);
        $otherReflectionFactory = new ReflectionFactory($className);

        $expected = $reflectionFactory->getInstance($arguments);

        $this->assertSame($expected, $reflectionFactory->getInstance($arguments));
        $this->assertSame($expected, $reflectionFactory->getInstance($arguments));

        $this->assertSame($expected, $otherReflectionFactory->getInstance($arguments));
        $this->assertSame($expected, $otherReflectionFactory->getInstance($arguments));
    }
}
