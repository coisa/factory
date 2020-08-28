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
 * Class StaticFactoryTest
 *
 * @package CoiSA\Factory
 */
final class StaticFactoryTest extends TestCase
{
    public function testCreateWithNonExistentClassWillThrowException()
    {
        $this->expectException('ReflectionException');
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
}
