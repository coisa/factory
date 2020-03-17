<?php

namespace CoiSA\Factory;

use PHPUnit\Framework\TestCase;

/**
 * Class CallableFactoryTest
 *
 * @package CoiSA\Factory
 */
final class CallableFactoryTest extends TestCase
{
    public function provideArguments()
    {
        return array(
            array(),
            array(array(1)),
            array(array(1, 2)),
            array(array(1, 2, 3)),
        );
    }

    /**
     * @dataProvider provideArguments
     */
    public function test_new_instance_will_return_same_as_callable_return($arguments = null)
    {
        $object = new \stdClass();
        $object->test = \uniqid('test', true);

        $callable = function () use ($object, $arguments) {
            $object->arguments = $arguments;

            return $object;
        };

        $factory = new CallableFactory($callable);

        $this->assertSame($object, $factory->newInstance($arguments));
    }

    /**
     * @dataProvider provideArguments
     */
    public function test_get_instance_will_return_same_as_callable_return($arguments = null)
    {
        $object = new \stdClass();
        $object->test = \uniqid('test', true);

        $callable = function () use ($object, $arguments) {
            $object->arguments = $arguments;

            return $object;
        };

        $factory = new CallableFactory($callable);

        $this->assertSame($object, $factory->getInstance());
    }

    /**
     * @dataProvider provideArguments
     */
    public function test_get_instance_will_return_always_same_instance($arguments = null)
    {
        $object = new \stdClass();
        $object->test = \uniqid('test', true);

        $callable = function () use ($object, $arguments) {
            $object->arguments = $arguments;

            return $object;
        };

        $factory = new CallableFactory($callable);

        $this->assertSame($object, $factory->getInstance());
        $this->assertSame($object, $factory->getInstance());
    }
}
