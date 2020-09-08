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

use CoiSA\Factory\Registry\FactoryRegistry;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Container\ContainerInterface;

/**
 * Class ContainerFactoryFactoryTest
 *
 * @package CoiSA\Factory
 */
final class ContainerFactoryFactoryTest extends TestCase
{
    /**
     * @var ContainerInterface|ObjectProphecy
     */
    private $container;

    /**
     * @var ContainerFactoryFactory
     */
    private $containerFactoryFactory;

    public function setUp()
    {
        $this->container               = $this->prophesize('Psr\\Container\\ContainerInterface');
        $this->containerFactoryFactory = new ContainerFactoryFactory($this->container->reveal());

        FactoryRegistry::setFactoryFactory($this->containerFactoryFactory);
    }

    public function testCreateWithoutEntryFromContainerWillReturnAbstractFactoryFactoryCreate()
    {
        $class                  = 'CoiSA\\Factory\\Stub\\ClassWithoutConstructor';
        $abstractFactoryFactory = new AbstractFactoryFactory();

        $this->container->has($class)->willReturn(false);

        $containerFactory = $this->containerFactoryFactory->createFactory($class);

        self::assertEquals(
            $abstractFactoryFactory->createFactory($class),
            $containerFactory
        );

        self::assertEquals(
            $containerFactory->create(),
            StaticFactory::create($class)
        );
    }

    public function testCreateWithEntryFromContainerWillReturnContainerFactoryThatCreateContainerGet()
    {
        $class = \uniqid('class', true);

        $object        = new \stdClass();
        $object->class = $class;

        $this->container->has($class)->willReturn(true);
        $this->container->get($class)->willReturn($object);

        $containerFactory = $this->containerFactoryFactory->createFactory($class);

        self::assertInstanceOf('CoiSA\\Factory\\FactoryInterface', $containerFactory);
        self::assertInstanceOf('CoiSA\\Factory\\ContainerFactory', $containerFactory);
        self::assertSame($object, $containerFactory->create());
        self::assertSame($object, StaticFactory::create($class));
    }
}
