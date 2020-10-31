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

use CoiSA\Factory\Stub\ClassWithoutConstructor;
use CoiSA\Factory\Stub\ConstructorWithMixedArgument;
use PHPUnit\Framework\TestCase;

/**
 * Class AbstractFactoryTest
 *
 * @package CoiSA\Factory
 */
final class AbstractFactoryTest extends TestCase
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

        $object = new \stdClass();
        $object->class = $class;

        $this->container->has($class)->willReturn(true);
        $this->container->get($class)->willReturn($object);

        AbstractFactory::setContainer($this->container->reveal());

        $containerFactory = AbstractFactory::create($class);

        self::assertInstanceOf('CoiSA\\Factory\\ContainerFactory', $containerFactory);
        self::assertSame($object, $containerFactory->create());
    }
}
