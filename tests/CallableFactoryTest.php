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

use CoiSA\Factory\Stub\ClassWithoutConstructor;
use CoiSA\Factory\Stub\ConstructorWithMixedArgument;
use PHPUnit\Framework\TestCase;

/**
 * Class CallableFactoryTest.
 *
 * @package CoiSA\Factory
 *
 * @internal
 * @coversNothing
 */
final class CallableFactoryTest extends TestCase
{
    public function testCreateWithInvalidCallableArgumentWillThrowInvalidArgumentException(): void
    {
        $this->expectException('CoiSA\\Factory\\Exception\\InvalidArgumentException');
        new CallableFactory(true);
    }

    public function testCreateWithouArgumentWillReturnCallableResult(): void
    {
        $callable = fn () => new ClassWithoutConstructor();

        $factory = new CallableFactory($callable);

        parent::assertInstanceOf('CoiSA\\Factory\\Stub\\ClassWithoutConstructor', $factory->create());
    }

    public function provideArguments()
    {
        return [
            [[1]],
            [[1, 2]],
            [[1, 2, 3]],
        ];
    }

    /**
     * @dataProvider provideArguments
     *
     * @param array $arguments
     */
    public function testCreateWithArgumentsWillReturnReturnCallableResult($arguments = null): void
    {
        $callable = fn () => new ConstructorWithMixedArgument(\func_get_args());

        $factory = new CallableFactory($callable);

        parent::assertInstanceOf(
            'CoiSA\\Factory\\Stub\\ConstructorWithMixedArgument',
            \call_user_func_array([$factory, 'create'], $arguments)
        );

        $object = \call_user_func_array([$factory, 'create'], $arguments);
        parent::assertSame($arguments, $object->getArgument());
    }
}
