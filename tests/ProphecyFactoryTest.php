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
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Class ProphecyFactoryTest.
 *
 * @package CoiSA\Factory
 *
 * @internal
 * @coversNothing
 */
final class ProphecyFactoryTest extends TestCase
{
    public function testCreateWithInvalidProphesizeMethodsCallableArgumentWillThrowInvalidArgumentException(): void
    {
        $objectProphecy = $this->prophesize('CoiSA\\Factory\\FactoryInterface');

        $this->expectException('InvalidArgumentException');
        new ProphecyFactory($objectProphecy, true);
    }

    public function testCreateWillReturnRevealedGivenObjectProphecy(): void
    {
        $objectProphecy = $this->prophesize('CoiSA\\Factory\\FactoryInterface');
        $factory        = new ProphecyFactory($objectProphecy);

        parent::assertSame($objectProphecy->reveal(), $factory->create());
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
    public function testCreateWillApplyProphesizeMethodCallableToObjectProphecyBeforeReveal(array $arguments = null): void
    {
        $objectProphecy = $this->prophesize('CoiSA\\Factory\\FactoryInterface');

        $factory = new ProphecyFactory(
            $objectProphecy,
            function (ObjectProphecy $objectProphecy, $arguments = null): void {
                $objectProphecy->create()->willReturn($arguments);
            }
        );

        parent::assertNotSame($objectProphecy, $factory->create($arguments));
        parent::assertSame($arguments, \call_user_func_array([$factory, 'create'], $arguments)->create());
    }
}
