<?php

declare(strict_types=1);

namespace CoiSA\Factory\Tests;

use CoiSA\Factory\Factory;
use CoiSA\Factory\Tests\Fixture\ArrayContainer;
use CoiSA\Factory\ValueFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

final class FactoryContainerMutationTest extends TestCase
{
    public function testLateInstallationKeepsAliasesOverridesAndTheSameResolver(): void
    {
        $resolver = new Factory();
        $injectedReference = $resolver;
        $resolver->alias('service', \stdClass::class);
        $resolver->set('override', new ValueFactory('fake'));
        $retainedAlias = $resolver->getFactory('service');
        $retainedReflection = $resolver->getFactory(\stdClass::class);
        $before = $resolver->create('service');
        $containerValue = new \stdClass();

        $resolver->setContainer(new ArrayContainer([
            \stdClass::class => $containerValue,
            'override' => 'container',
        ]));

        self::assertSame($resolver, $injectedReference);
        self::assertSame($containerValue, $injectedReference->create('service'));
        self::assertSame($containerValue, $retainedAlias->create());
        self::assertSame('fake', $resolver->create('override'));
        self::assertNotSame($before, $containerValue);
        self::assertNotSame($containerValue, $retainedReflection->create());
    }

    public function testSwapAndRemovalAffectNewSelectionsButNotCapturedContainerFactories(): void
    {
        $old = (object) ['source' => 'old'];
        $new = (object) ['source' => 'new'];
        $resolver = new Factory(new ArrayContainer([\stdClass::class => $old]));
        $captured = $resolver->getFactory(\stdClass::class);

        $resolver->setContainer(new ArrayContainer([\stdClass::class => $new]));
        self::assertSame($new, $resolver->create(\stdClass::class));
        self::assertSame($old, $captured->create());
        self::assertSame('old', $old->source);

        $resolver->setContainer(null);
        $reflected = $resolver->create(\stdClass::class);
        self::assertInstanceOf(\stdClass::class, $reflected);
        self::assertNotSame($old, $reflected);
        self::assertNotSame($new, $reflected);
        self::assertSame($old, $captured->create());
    }

    public function testContainerMutationDoesNotAffectAnotherResolver(): void
    {
        $first = new Factory();
        $second = new Factory();
        $selected = new \stdClass();
        $first->setContainer(new ArrayContainer([\stdClass::class => $selected]));

        self::assertSame($selected, $first->create(\stdClass::class));
        self::assertNotSame($selected, $second->create(\stdClass::class));
    }

    public function testAnInFlightSelectionKeepsTheContainerThatWasConsulted(): void
    {
        $old = new \stdClass();
        $new = new \stdClass();
        $container = new class ($old) implements ContainerInterface {
            public function __construct(private object $value)
            {
            }

            public function has(string $id): bool
            {
                \Fiber::suspend('selecting');
                return true;
            }

            public function get(string $id): mixed
            {
                return $this->value;
            }
        };
        $resolver = new Factory($container);
        $pending = new \Fiber(fn () => $resolver->create(\stdClass::class));
        self::assertSame('selecting', $pending->start());
        $resolver->setContainer(new ArrayContainer([\stdClass::class => $new]));
        $pending->resume();

        self::assertSame($old, $pending->getReturn());
        self::assertSame($new, $resolver->create(\stdClass::class));
    }
}
