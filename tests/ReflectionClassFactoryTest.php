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
 * Class ReflectionClassFactoryTest.
 *
 * @package CoiSA\Factory
 *
 * @internal
 * @coversNothing
 */
final class ReflectionClassFactoryTest extends TestCase
{
    public function testConstructorWithNonExistentClassWillThrowException(): void
    {
        $className = __NAMESPACE__ . '\\' . uniqid('Test', false);

        $this->expectException('CoiSA\\Factory\\Exception\\ReflectionException');
        new ReflectionClassFactory($className);
    }

    public function testCreateWithoutArgumentsReturnObjectOnClassWithoutConstructor(): void
    {
        $className         = __NAMESPACE__ . '\\Stub\\ClassWithoutConstructor';
        $reflectionFactory = new ReflectionClassFactory($className);

        $object = $reflectionFactory->create();

        parent::assertInstanceOf(__NAMESPACE__ . '\\Stub\\ClassWithoutConstructor', $object);
    }

    public function testCreateWithoutArgumentsReturnInitializedObjectOnClassWithoutArgumentConstructor(): void
    {
        $className         = __NAMESPACE__ . '\\Stub\\ConstructorWithoutArgument';
        $reflectionFactory = new ReflectionClassFactory($className);

        $object = $reflectionFactory->create();

        parent::assertInstanceOf(__NAMESPACE__ . '\\Stub\\ConstructorWithoutArgument', $object);
        parent::assertStringStartsWith('test', $object->argument);
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
     */
    public function testCreateWithArgumentsWillInstantiateConstructorWithArguments(array $arguments): void
    {
        $className         = __NAMESPACE__ . '\\Stub\\ConstructorWithMixedArgument';
        $reflectionFactory = new ReflectionClassFactory($className);

        $object = $reflectionFactory->create($arguments);

        parent::assertSame($arguments, $object->getArgument());
    }
}
