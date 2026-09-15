<?php

declare(strict_types=1);

namespace CoiSA\Factory\Tests;

use CoiSA\Factory\AbstractFactoryFactory;
use CoiSA\Factory\CallableFactory;
use CoiSA\Factory\Exception\FactoryExceptionInterface;
use CoiSA\Factory\Exception\InvalidArgumentException;
use CoiSA\Factory\Exception\ReflectionException;
use CoiSA\Factory\Factory;
use CoiSA\Factory\ReflectionAttributeFactory;
use CoiSA\Factory\ReflectionClassFactory;
use CoiSA\Factory\Tests\Fixture\NoConstructor;
use CoiSA\Factory\Tests\Fixture\Product;
use CoiSA\Factory\Tests\Fixture\RealService;
use CoiSA\Factory\Tests\Fixture\Service;
use CoiSA\Factory\Tests\Fixture\StaticFactory;
use CoiSA\Factory\ValueFactory;
use PHPUnit\Framework\TestCase;

final class StrategiesTest extends TestCase
{
    public function testCallableForwardsBothArgumentFormsAndReturnsScalars(): void
    {
        $factory = new CallableFactory(static fn (string $id, int $count = 1): string => $id . ':' . $count);
        self::assertSame('x:2', $factory->create('x', 2));
        self::assertSame('y:3', $factory->create(count: 3, id: 'y'));
        self::assertSame('static-real', (new CallableFactory([RealService::class, 'direct']))->create());
    }

    public function testInvalidCallableHasTheNativePhpTypeErrorContract(): void
    {
        $this->expectException(\TypeError::class);
        new CallableFactory('not_a_callable');
    }

    public function testValueFactoryPreservesIdentityNullAndScalarsAndIgnoresConstructionArguments(): void
    {
        $object = new \stdClass();
        self::assertSame($object, (new ValueFactory($object))->create(unused: 'ignored'));
        self::assertNull((new ValueFactory(null))->create());
        self::assertFalse((new ValueFactory(false))->create());
        self::assertSame(42, (new ValueFactory(42))->create());
    }

    public function testReflectionConstructsFinalAndSupportedInternalClassesNormally(): void
    {
        self::assertInstanceOf(RealService::class, (new ReflectionClassFactory(RealService::class))->create());
        $date = (new ReflectionClassFactory(\DateTimeImmutable::class))->create(datetime: '2026-01-02');
        self::assertSame('2026-01-02', $date->format('Y-m-d'));
        self::assertInstanceOf(NoConstructor::class, (new ReflectionClassFactory(new NoConstructor()))->create());
    }

    public function testMissingClassUsesTheExactLocalReflectionExceptionWithPrevious(): void
    {
        try {
            (new Factory())->create('NoSuchServiceClass');
            self::fail('Expected reflection error.');
        } catch (ReflectionException $exception) {
            self::assertSame(ReflectionException::class, $exception::class);
            self::assertInstanceOf(FactoryExceptionInterface::class, $exception);
            self::assertInstanceOf(\ReflectionException::class, $exception->getPrevious());
        }
    }

    public function testInterfacesCannotBeConstructedWithoutAnOverride(): void
    {
        $this->expectException(ReflectionException::class);
        new ReflectionClassFactory(Service::class);
    }

    public function testReflectionDoesNotBypassPrivateOrUnsupportedInternalConstructors(): void
    {
        $this->expectException(ReflectionException::class);
        new ReflectionClassFactory(\Closure::class);
    }

    public function testConstructorTypeFailuresRemainNative(): void
    {
        $this->expectException(\TypeError::class);
        (new ReflectionClassFactory(Product::class))->create(id: []);
    }

    public function testUnknownNamedArgumentsAreNotDiscarded(): void
    {
        $this->expectException(\Error::class);
        (new ReflectionClassFactory(Product::class))->create(unknown: 'argument');
    }

    public function testMissingAttributeIsAnExplicitErrorForTheAttributeAdapter(): void
    {
        $this->expectException(ReflectionException::class);
        new ReflectionAttributeFactory(Product::class);
    }

    public function testStaticAdapterForwardsNamedArguments(): void
    {
        self::assertSame(
            ['id' => '42'],
            (new AbstractFactoryFactory(StaticFactory::class))->create(id: '42')->arguments,
        );
    }

    public function testStaticAdapterRejectsAnUnrelatedClass(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new AbstractFactoryFactory(Product::class);
    }

    public function testSubstitutionOnlyChangesTheExplicitSeamAndRespectsConsumerTypes(): void
    {
        $fake = new class implements Service {
            public function run(): string
            {
                return 'fake';
            }
        };
        $factory = new Factory();
        $factory->set(RealService::class, new ValueFactory($fake));
        $consume = static fn (Service $service): string => $service->run();
        self::assertSame('fake', $consume($factory->create(RealService::class)));
        self::assertSame('real', (new RealService())->run());
        self::assertSame('static-real', RealService::direct());

        $concreteConsumer = static fn (RealService $service): string => $service->run();
        $this->expectException(\TypeError::class);
        $concreteConsumer($factory->create(RealService::class));
    }
}
