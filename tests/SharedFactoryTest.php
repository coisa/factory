<?php

namespace CoiSA\Factory;

use PHPUnit\Framework\TestCase;

/**
 * Class SharedFactoryTest
 *
 * @package CoiSA\Factory
 */
final class SharedFactoryTest extends TestCase
{
    /**
     * @var SharedFactory
     */
    private $sharedFactory;

    public function setUp()
    {
        $this->sharedFactory = new SharedFactory();
    }

    public function provideGetSharedArguments()
    {
        return array(
            array(__NAMESPACE__ . '\\Stub\\ClassWithoutConstructor'),
            array(__NAMESPACE__ . '\\Stub\\ConstructorWithoutArgument'),
            array(__NAMESPACE__ . '\\Stub\\ConstructorWithMixedArgument', array(\uniqid('test', true))),
            array(__NAMESPACE__ . '\\Stub\\ConstructorWithTypedArgument', array(array(\uniqid('test', true)))),
        );
    }

    public function provideClassNamesWithArgument()
    {
        return array(
            array(__NAMESPACE__ . '\\Stub\\ConstructorWithMixedArgument'),
            array(__NAMESPACE__ . '\\Stub\\ConstructorWithTypedArgument'),
        );
    }

    /**
     * @dataProvider provideGetSharedArguments
     */
    public function test_get_shared_without_argument_will_return_same_instance($className, array $arguments = null)
    {
        $expected = $this->sharedFactory->getShared($className, $arguments);

        $this->assertSame($expected, $this->sharedFactory->getShared($className, $arguments));
        $this->assertSame($expected, $this->sharedFactory->getShared($className, $arguments));

        $sharedFactory = new SharedFactory();

        $this->assertSame($expected, $sharedFactory->getShared($className, $arguments));
        $this->assertSame($expected, $sharedFactory->getShared($className, $arguments));
    }
}
