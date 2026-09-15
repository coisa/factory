<?php

declare(strict_types=1);

namespace CoiSA\Factory\Tests;

use CoiSA\Factory\ProphecyFactory;
use CoiSA\Factory\ProphesizeFactory;
use CoiSA\Factory\Tests\Fixture\RealService;
use CoiSA\Factory\Tests\Fixture\Service;
use PHPUnit\Framework\TestCase;
use Prophecy\Exception\Doubler\ClassMirrorException;
use Prophecy\Exception\Prediction\AggregateException;
use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\Prophet;

final class ProphecyAdaptersTest extends TestCase
{
    public function testExistingProphecyIsReusedAndVerifiedByItsOwner(): void
    {
        $prophet = new Prophet();
        $prophecy = $prophet->prophesize(Service::class);
        $factory = new ProphecyFactory($prophecy, static function (ObjectProphecy $double, array $arguments): void {
            $double->run()->willReturn($arguments['result'])->shouldBeCalledOnce();
        });
        $service = $factory->create(result: 'fake');
        self::assertSame($prophecy->reveal(), $service);
        self::assertSame('fake', $service->run());
        $prophet->checkPredictions();
    }

    public function testCallerOwnedProphetTracksFreshDoublesAndNamedConfigurationArguments(): void
    {
        $prophet = new Prophet();
        $factory = new ProphesizeFactory(
            $prophet,
            Service::class,
            static function (ObjectProphecy $double, array $arguments): void {
                $double->run()->willReturn($arguments['result'])->shouldBeCalledOnce();
            },
        );
        $first = $factory->create(result: 'first');
        $second = $factory->create(result: 'second');
        self::assertNotSame($first, $second);
        self::assertSame('first', $first->run());
        self::assertSame('second', $second->run());
        $prophet->checkPredictions();
    }

    public function testUnusedPredictionFailsWhenTheOwnerVerifiesIt(): void
    {
        $prophet = new Prophet();
        $factory = new ProphesizeFactory($prophet, Service::class, static function (ObjectProphecy $double): void {
            $double->run()->shouldBeCalled();
        });
        $factory->create();
        $this->expectException(AggregateException::class);
        $prophet->checkPredictions();
    }

    public function testStubWithoutConfigurationCanBeRevealed(): void
    {
        $prophet = new Prophet();
        self::assertInstanceOf(Service::class, (new ProphesizeFactory($prophet, Service::class))->create());
        self::assertInstanceOf(Service::class, (new ProphecyFactory($prophet->prophesize(Service::class)))->create());
        $prophet->checkPredictions();
    }

    public function testFinalClassesAreNotPromisedAsMockable(): void
    {
        $this->expectException(ClassMirrorException::class);
        (new ProphesizeFactory(new Prophet(), RealService::class))->create();
    }
}
