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
     *
     * @param null|mixed $arguments
     */
    public function testNewInstanceWillReturnSameAsCallableReturn($arguments = null)
    {
        $object       = new \stdClass();
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
     *
     * @param null|mixed $arguments
     */
    public function testGetInstanceWillReturnSameAsCallableReturn($arguments = null)
    {
        $object       = new \stdClass();
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
     *
     * @param null|mixed $arguments
     */
    public function testGetInstanceWillReturnAlwaysSameInstance($arguments = null)
    {
        $object       = new \stdClass();
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
