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
 * Class ProphesizeFactoryTest.
 *
 * @package CoiSA\Factory
 *
 * @internal
 * @coversNothing
 */
final class ProphesizeFactoryTest extends TestCase
{
    public function testCreateWithInvalidProphesizeMethodsCallableArgumentWillThrowInvalidArgumentException(): void
    {
        $this->expectException('CoiSA\\Factory\\Exception\\InvalidArgumentException');
        new ProphesizeFactory('CoiSA\\Factory\\FactoryInterface', true);
    }

    public function provideClassOrIterface()
    {
        return [
            ['CoiSA\\Factory\\Stub\\ClassWithoutConstructor'],
            ['CoiSA\\Factory\\FactoryInterface'],
        ];
    }

    /**
     * @dataProvider provideClassOrIterface
     *
     * @param string $classOrInterface
     */
    public function testCreateWillReturnRevealedGivenClassOrInterface($classOrInterface): void
    {
        $factory = new ProphesizeFactory($classOrInterface);

        parent::assertInstanceOf($classOrInterface, $factory->create());
    }

    /**
     * @dataProvider provideClassOrIterface
     *
     * @param string $classOrInterface
     */
    public function testCreateWillReturnDifferentRevealedGivenClassOrInterfaceEveryCall($classOrInterface): void
    {
        $factory = new ProphesizeFactory($classOrInterface);

        parent::assertInstanceOf($classOrInterface, $factory->create());
        parent::assertNotSame($factory->create(), $factory->create());
    }

    /**
     * @dataProvider provideClassOrIterface
     *
     * @param string $classOrInterface
     */
    public function testCreateeWillApplyProphesizeMethodToGivenClassOrInterfaceBeforeReveal($classOrInterface): void
    {
        $id = random_int(1, 1000);

        $factory = new ProphesizeFactory(
            $classOrInterface,
            function (ObjectProphecy $objectProphecy, $arguments = null) use ($id): void {
                $objectProphecy->id = $id;
            }
        );

        parent::assertSame($id, $factory->create()->id);
    }
}
