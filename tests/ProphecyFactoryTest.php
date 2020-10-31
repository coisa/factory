<?php

/**
 * This file is part of coisa/factory.
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * @link      https://github.com/coisa/factory
 *
 * @copyright Copyright (c) 2020 Felipe Sayão Lobato Abreu <github@felipeabreu.com.br>
 * @license   https://opensource.org/licenses/MIT MIT License
 */
namespace CoiSA\Factory;

use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class ProphecyFactoryTest.
 *
 * @package CoiSA\Factory
 */
final class ProphecyFactoryTest extends TestCase
{
    public function testCreateWithInvalidProphesizeMethodsCallableArgumentWillThrowInvalidArgumentException()
    {
        $objectProphecy = $this->prophesize('CoiSA\\Factory\\FactoryInterface');

        $this->setExpectedException('InvalidArgumentException');
        new ProphecyFactory($objectProphecy, true);
    }

    public function testCreateWillReturnRevealedGivenObjectProphecy()
    {
        $objectProphecy = $this->prophesize('CoiSA\\Factory\\FactoryInterface');
        $factory        = new ProphecyFactory($objectProphecy);

        self::assertSame($objectProphecy->reveal(), $factory->create());
    }

    public function provideArguments()
    {
        return array(
            array(array(1)),
            array(array(1, 2)),
            array(array(1, 2, 3)),
        );
    }

    /**
     * @dataProvider provideArguments
     */
    public function testCreateWillApplyProphesizeMethodCallableToObjectProphecyBeforeReveal(array $arguments = null)
    {
        $objectProphecy = $this->prophesize('CoiSA\\Factory\\FactoryInterface');

        $factory = new ProphecyFactory(
            $objectProphecy,
            function(ObjectProphecy $objectProphecy, $arguments = null) {
                $objectProphecy->create()->willReturn($arguments);
            }
        );

        self::assertNotSame($objectProphecy, $factory->create($arguments));
        self::assertEquals($arguments, \call_user_func_array(array($factory, 'create'), $arguments)->create());
    }
}
