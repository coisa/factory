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
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class ProphesizeFactoryTest
 *
 * @package CoiSA\Factory
 */
final class ProphesizeFactoryTest extends TestCase
{
    public function testCreateWithInvalidProphesizeMethodsCallableArgumentWillThrowInvalidArgumentException()
    {
        $this->setExpectedException('CoiSA\\Factory\\Exception\\InvalidArgumentException');
        new ProphesizeFactory('CoiSA\\Factory\\FactoryInterface', true);
    }

    public function provideClassOrIterface()
    {
        return array(
            array('CoiSA\\Factory\\Stub\\ClassWithoutConstructor'),
            array('CoiSA\\Factory\\FactoryInterface'),
        );
    }

    /**
     * @dataProvider provideClassOrIterface
     *
     * @param mixed $classOrInterface
     */
    public function testCreateWillReturnRevealedGivenClassOrInterface($classOrInterface)
    {
        $factory = new ProphesizeFactory($classOrInterface);

        self::assertInstanceOf($classOrInterface, $factory->create());
    }

    /**
     * @dataProvider provideClassOrIterface
     *
     * @param mixed $classOrInterface
     */
    public function testCreateWillReturnDifferentRevealedGivenClassOrInterfaceEveryCall($classOrInterface)
    {
        $factory = new ProphesizeFactory($classOrInterface);

        self::assertInstanceOf($classOrInterface, $factory->create());
        self::assertNotSame($factory->create(), $factory->create());
    }

    /**
     * @dataProvider provideClassOrIterface
     *
     * @param mixed $classOrInterface
     */
    public function testCreateeWillApplyProphesizeMethodToGivenClassOrInterfaceBeforeReveal($classOrInterface)
    {
        $id = \mt_rand(1, 1000);

        $factory = new ProphesizeFactory(
            $classOrInterface,
            function (ObjectProphecy $objectProphecy, $arguments = null) use ($id) {
                $objectProphecy->id = $id;
            }
        );

        self::assertEquals($id, $factory->create()->id);
    }
}
