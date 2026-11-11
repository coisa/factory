<?php

declare(strict_types=1);

namespace CoiSA\Factory\Tests;

use CoiSA\Factory\CallableFactory;
use CoiSA\Factory\LegacyFactory;
use CoiSA\Factory\Tests\Fixture\AutowiredDependency;
use CoiSA\Factory\Tests\Fixture\AutowiredProduct;
use CoiSA\Factory\Tests\Fixture\AutowiredProductFactory;
use DI\Container;
use DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;

final class AutowiringIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        LegacyFactory::reset();
    }

    protected function tearDown(): void
    {
        LegacyFactory::reset();
    }

    public function testRealContainerAutowiresAnOrdinaryClassWithoutMetadata(): void
    {
        $container = (new ContainerBuilder())->useAutowiring(true)->useAttributes(false)->build();
        LegacyFactory::setContainer($container);
        $product = LegacyFactory::create(AutowiredProduct::class);

        self::assertInstanceOf(AutowiredProduct::class, $product);
        self::assertSame($container->get(AutowiredDependency::class), $product->dependency);
        self::assertSame($product, LegacyFactory::create(AutowiredProduct::class));
    }

    public function testAnAutowiredFactoryObjectCanBeExplicitlyRegisteredForCallSpecificArguments(): void
    {
        $container = new Container();
        LegacyFactory::setContainer($container);
        $builder = LegacyFactory::create(AutowiredProductFactory::class);
        self::assertInstanceOf(AutowiredProductFactory::class, $builder);
        self::assertSame($container->get(AutowiredDependency::class), $builder->dependency);

        LegacyFactory::resolver()->set(AutowiredProduct::class, $builder);
        $first = LegacyFactory::create(AutowiredProduct::class, ['label' => 'first']);
        $second = LegacyFactory::create(AutowiredProduct::class, ['label' => 'second']);
        self::assertNotSame($first, $second);
        self::assertSame('first', $first->label);
        self::assertSame('second', $second->label);
        self::assertSame($builder->dependency, $second->dependency);
    }

    public function testAnExplicitLazyFactoryBindingSeesTheReplacementContainer(): void
    {
        $old = new AutowiredDependency('old');
        $new = new AutowiredDependency('new');
        $resolver = LegacyFactory::resolver();
        $resolver->set(AutowiredProduct::class, new CallableFactory(
            static fn (mixed ...$arguments): AutowiredProduct => $resolver
                ->create(AutowiredProductFactory::class)
                ->create(...$arguments),
        ));
        $resolver->alias('report', AutowiredProduct::class);
        LegacyFactory::setContainer(new Container([AutowiredDependency::class => $old]));
        $first = LegacyFactory::create('report', ['label' => 'first']);

        LegacyFactory::setContainer(new Container([AutowiredDependency::class => $new]));
        $second = LegacyFactory::create('report', ['label' => 'second']);
        self::assertSame($old, $first->dependency);
        self::assertSame($new, $second->dependency);
        self::assertSame('second', $second->label);
    }

    public function testResetDetachesButDoesNotPurgeTheContainerCache(): void
    {
        $container = new Container();
        LegacyFactory::setContainer($container);
        $first = LegacyFactory::create(AutowiredProduct::class);
        LegacyFactory::reset();

        LegacyFactory::setContainer($container);
        self::assertSame($first, LegacyFactory::create(AutowiredProduct::class));
        LegacyFactory::reset();
        LegacyFactory::setContainer(new Container());
        self::assertNotSame($first, LegacyFactory::create(AutowiredProduct::class));
    }
}
