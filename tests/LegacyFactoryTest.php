<?php

declare(strict_types=1);

namespace CoiSA\Factory\Tests;

use CoiSA\Factory\Factory;
use CoiSA\Factory\LegacyFactory;
use CoiSA\Factory\Tests\Fixture\ArrayContainer;
use CoiSA\Factory\ValueFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

final class LegacyFactoryTest extends TestCase
{
    protected function setUp(): void
    {
        LegacyFactory::reset();
    }

    protected function tearDown(): void
    {
        LegacyFactory::reset();
    }

    public function testStaticCallsWorkBeforeInstallationAndKeepTheirResolverAfterward(): void
    {
        $resolver = LegacyFactory::resolver();
        $resolver->alias('service', \stdClass::class);
        $resolver->set('override', new ValueFactory('fake'));
        $retainedAlias = LegacyFactory::getFactory('service');
        $before = LegacyFactory::create('service');
        $installed = new \stdClass();

        LegacyFactory::setContainer(new ArrayContainer([
            \stdClass::class => $installed,
            'override' => 'container',
        ]));

        self::assertInstanceOf(\stdClass::class, $before);
        self::assertNotSame($before, $installed);
        self::assertSame($resolver, LegacyFactory::resolver());
        self::assertSame($installed, LegacyFactory::create('service'));
        self::assertSame($installed, $retainedAlias->create());
        self::assertSame('fake', LegacyFactory::create('override'));
    }

    public function testSwappingAndRemovingTheContainerKeepExplicitRegistrations(): void
    {
        $old = new \stdClass();
        $new = new \stdClass();
        LegacyFactory::resolver()->alias('service', \stdClass::class);
        LegacyFactory::resolver()->set('override', new ValueFactory('fake'));
        LegacyFactory::setContainer(new ArrayContainer([\stdClass::class => $old]));
        $captured = LegacyFactory::getFactory(\stdClass::class);

        LegacyFactory::setContainer(new ArrayContainer([\stdClass::class => $new]));
        self::assertSame($new, LegacyFactory::create('service'));
        self::assertSame($old, $captured->create());

        LegacyFactory::setContainer(null);
        self::assertNotSame($new, LegacyFactory::create('service'));
        self::assertSame('fake', LegacyFactory::create('override'));
    }

    public function testResetClearsStateForAlreadyInjectedResolverReferences(): void
    {
        $resolver = LegacyFactory::resolver();
        $containerValue = new \stdClass();
        $container = new ArrayContainer([\stdClass::class => $containerValue]);
        $resolver->set('override', new ValueFactory('fake'));
        $resolver->alias('alias', 'override');
        LegacyFactory::setContainer($container);
        $captured = LegacyFactory::getFactory(\stdClass::class);

        LegacyFactory::reset();

        self::assertSame($resolver, LegacyFactory::resolver());
        self::assertFalse($resolver->has('override'));
        self::assertFalse($resolver->has('alias'));
        self::assertNotSame($containerValue, $resolver->create(\stdClass::class));
        self::assertSame($containerValue, $captured->create());
        self::assertSame($containerValue, $container->get(\stdClass::class));
    }

    public function testFinallyResetPreventsStateLeakingIntoTheNextSequentialJob(): void
    {
        $resolver = LegacyFactory::resolver();
        $error = new \LogicException('job failed');
        try {
            try {
                LegacyFactory::resolver()->set('job', new ValueFactory('first'));
                LegacyFactory::setContainer(new ArrayContainer([\stdClass::class => 'first container']));
                self::assertSame('first', LegacyFactory::create('job'));
                throw $error;
            } finally {
                LegacyFactory::reset();
            }
        } catch (\LogicException $caught) {
            self::assertSame($error, $caught);
        }

        self::assertSame($resolver, LegacyFactory::resolver());
        self::assertFalse($resolver->has('job'));
        self::assertInstanceOf(\stdClass::class, LegacyFactory::create(\stdClass::class));
        LegacyFactory::resolver()->set('job', new ValueFactory('second'));
        self::assertSame('second', LegacyFactory::create('job'));
    }

    public function testSelectedErrorsPropagateAndAReplacementContainerCanRecover(): void
    {
        $error = new \LogicException('broken definition');
        $container = new class ($error) implements ContainerInterface {
            public function __construct(private \Throwable $error)
            {
            }

            public function has(string $id): bool
            {
                return true;
            }

            public function get(string $id): mixed
            {
                throw $this->error;
            }
        };
        LegacyFactory::setContainer($container);
        try {
            LegacyFactory::create(\stdClass::class);
            self::fail('Selected errors must not fall back to reflection.');
        } catch (\LogicException $caught) {
            self::assertSame($error, $caught);
        }

        $recovered = new \stdClass();
        LegacyFactory::setContainer(new ArrayContainer([\stdClass::class => $recovered]));
        self::assertSame($recovered, LegacyFactory::create(\stdClass::class));
    }

    public function testDefaultInstancesDoNotShareTheOptInStaticBridge(): void
    {
        LegacyFactory::resolver()->set(\stdClass::class, new ValueFactory('legacy fake'));
        $independent = new Factory();
        self::assertSame('legacy fake', LegacyFactory::create(\stdClass::class));
        self::assertInstanceOf(\stdClass::class, $independent->create(\stdClass::class));
        $independent->set(\stdClass::class, new ValueFactory('instance fake'));
        LegacyFactory::reset();
        self::assertSame('instance fake', $independent->create(\stdClass::class));
    }
}
