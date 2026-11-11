<?php

declare(strict_types=1);

namespace CoiSA\Factory\Tests;

use CoiSA\Factory\CallableFactory;
use CoiSA\Factory\Exception\CircularDependencyException;
use CoiSA\Factory\Factory;
use CoiSA\Factory\Registry\FactoryRegistry;
use CoiSA\Factory\Tests\Fixture\ArrayContainer;
use CoiSA\Factory\Tests\Fixture\AttributeAndStatic;
use CoiSA\Factory\Tests\Fixture\AttributedProduct;
use CoiSA\Factory\Tests\Fixture\BrokenMetadata;
use CoiSA\Factory\Tests\Fixture\InjectedFactory;
use CoiSA\Factory\Tests\Fixture\InjectedMetadata;
use CoiSA\Factory\Tests\Fixture\InvalidMetadata;
use CoiSA\Factory\Tests\Fixture\Product;
use CoiSA\Factory\Tests\Fixture\RepeatedMetadata;
use CoiSA\Factory\Tests\Fixture\StaticFactory;
use CoiSA\Factory\ValueFactory;
use PHPUnit\Framework\TestCase;

final class FactoryTest extends TestCase
{
    public function testPositionalAndNamedArgumentsReachTheConstructorWithoutIdentifierCollisions(): void
    {
        $factory = new Factory();
        self::assertEquals(new Product('42', 'blue'), $factory->create(Product::class, ['42', 'blue']));
        self::assertEquals(
            new Product('42', 'blue'),
            $factory->create(Product::class, ['label' => 'blue', 'id' => '42']),
        );
        self::assertNotSame($factory->create(Product::class, ['1']), $factory->create(Product::class, ['1']));
    }

    public function testRegistrationsAreIsolatedAndRemovalRestoresDefaultResolution(): void
    {
        $left = new Factory();
        $right = new Factory();
        $left->set(Product::class, new ValueFactory('override'));
        self::assertTrue($left->has(Product::class));
        self::assertFalse($right->has(Product::class));
        self::assertSame('override', $left->create(Product::class));
        self::assertInstanceOf(Product::class, $right->create(Product::class, ['right']));
        self::assertTrue($left->remove(Product::class));
        self::assertFalse($left->remove(Product::class));
        self::assertInstanceOf(Product::class, $left->create(Product::class, ['restored']));
        $left->set('one', new ValueFactory(1));
        $left->set('two', new ValueFactory(2));
        $left->clear();
        self::assertFalse($left->has('one'));
        self::assertFalse($left->has('two'));
    }

    public function testSharingARegistryIsAnExplicitChoice(): void
    {
        $registry = new FactoryRegistry();
        $left = new Factory(registry: $registry);
        $right = new Factory(registry: $registry);
        $left->set('shared', new ValueFactory(42));
        self::assertSame(42, $right->create('shared'));
    }

    public function testPrecedenceIsRegistrationThenContainerThenAttributeThenStaticThenReflection(): void
    {
        $container = new ArrayContainer([AttributeAndStatic::class => 'container']);
        $factory = new Factory($container);
        $factory->set(AttributeAndStatic::class, new ValueFactory('registry'));
        self::assertSame('registry', $factory->create(AttributeAndStatic::class));
        self::assertSame(0, $container->reads);
        $factory->remove(AttributeAndStatic::class);
        self::assertSame('container', $factory->create(AttributeAndStatic::class));

        $withoutContainer = new Factory();
        self::assertSame(1, $withoutContainer->create(AttributeAndStatic::class)->call);
        self::assertSame('static', $withoutContainer->create(StaticFactory::class)->strategy);
        self::assertInstanceOf(Product::class, $withoutContainer->create(Product::class, ['reflection']));
    }

    public function testMetadataFactoryIsFreshAndCanUseAnInjectedRegistration(): void
    {
        $factory = new Factory();
        self::assertSame(1, $factory->create(AttributedProduct::class)->call);
        self::assertSame(1, $factory->create(AttributedProduct::class)->call);
        $factory->set('injected.builder', new InjectedFactory('prefix:'));
        self::assertSame('prefix:value', $factory->create(InjectedMetadata::class, ['value']));
    }

    public function testSelectedMetadataConstructionFailureIsNeverHiddenByFallback(): void
    {
        BrokenMetadata::$fallbackConstructions = 0;
        try {
            (new Factory())->create(BrokenMetadata::class);
            self::fail('The selected factory must fail.');
        } catch (\LogicException $exception) {
            self::assertSame('factory construction failed', $exception->getMessage());
            self::assertSame(0, BrokenMetadata::$fallbackConstructions);
        }
    }

    public function testInvalidAttributeArgumentsPropagate(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('Unknown named parameter');
        (new Factory())->create(InvalidMetadata::class);
    }

    public function testRepeatedAttributesAreNotSilentlyAccepted(): void
    {
        $this->expectException(\Error::class);
        $this->expectExceptionMessage('must not be repeated');
        (new Factory())->create(RepeatedMetadata::class);
    }

    public function testAliasesResolveAtCallTimeAndForwardNamedArguments(): void
    {
        $factory = new Factory();
        $factory->alias('a', 'b');
        $factory->alias('b', Product::class);
        self::assertEquals(new Product('alias'), $factory->create('a', ['id' => 'alias']));
        $factory->set(Product::class, new ValueFactory('new target'));
        self::assertSame('new target', $factory->getFactory('a')->create());
    }

    public function testAliasCyclesReportTheirPathAndDoNotPoisonLaterCalls(): void
    {
        $factory = new Factory();
        $factory->alias('a', 'b');
        $factory->alias('b', 'a');
        try {
            $factory->create('a');
            self::fail('Expected a cycle.');
        } catch (CircularDependencyException $exception) {
            self::assertSame('Circular factory resolution: a -> b -> a', $exception->getMessage());
        }
        $factory->set('b', new ValueFactory('recovered'));
        self::assertSame('recovered', $factory->create('a'));
    }

    public function testSelfAliasAndRecursiveCallbacksAreDetected(): void
    {
        $factory = new Factory();
        $factory->alias('self', 'self');
        try {
            $factory->create('self');
            self::fail('Expected self cycle.');
        } catch (CircularDependencyException $exception) {
            self::assertStringContainsString('self -> self', $exception->getMessage());
        }
        $factory->set('callback', new CallableFactory(fn () => $factory->create('callback')));
        $this->expectException(CircularDependencyException::class);
        $factory->create('callback');
    }

    public function testResolutionStateIsReleasedWhenUserCodeThrows(): void
    {
        $factory = new Factory();
        $error = new \DomainException('user failure');
        $factory->set('operation', new CallableFactory(static fn () => throw $error));
        try {
            $factory->create('operation');
            self::fail('Expected user failure.');
        } catch (\DomainException $caught) {
            self::assertSame($error, $caught);
        }
        $factory->set('operation', new ValueFactory('retry'));
        self::assertSame('retry', $factory->create('operation'));
    }

    public function testSuspendedFibersDoNotLookLikeCircularDependencies(): void
    {
        $factory = new Factory();
        $factory->set('yielding', new CallableFactory(static function (string $value): string {
            \Fiber::suspend();
            return $value;
        }));
        $first = new \Fiber(fn () => $factory->create('yielding', ['first']));
        $second = new \Fiber(fn () => $factory->create('yielding', ['second']));
        $first->start();
        $second->start();
        $second->resume();
        $first->resume();
        self::assertSame('first', $first->getReturn());
        self::assertSame('second', $second->getReturn());
    }
}
