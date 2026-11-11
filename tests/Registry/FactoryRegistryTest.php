<?php

declare(strict_types=1);

namespace CoiSA\Factory\Tests\Registry;

use CoiSA\Factory\Exception\FactoryExceptionInterface;
use CoiSA\Factory\Exception\InvalidArgumentException;
use CoiSA\Factory\Exception\OutOfBoundsException;
use CoiSA\Factory\Exception\ReflectionException;
use CoiSA\Factory\Registry\FactoryRegistry;
use CoiSA\Factory\Tests\Fixture\AbstractStaticFactory;
use CoiSA\Factory\Tests\Fixture\CountingFactory;
use CoiSA\Factory\Tests\Fixture\InjectedFactory;
use CoiSA\Factory\Tests\Fixture\StaticFactory;
use CoiSA\Factory\ValueFactory;
use PHPUnit\Framework\TestCase;

final class FactoryRegistryTest extends TestCase
{
    public function testFactoryObjectsAreReusedButClassDefinitionsAreFresh(): void
    {
        $registry = new FactoryRegistry();
        $object = new CountingFactory();
        $registry->set('object', $object);
        self::assertSame($object, $registry->get('object'));
        self::assertSame(1, $registry->get('object')->create()->call);
        self::assertSame(2, $registry->get('object')->create()->call);
        $registry->set('class', CountingFactory::class);
        self::assertNotSame($registry->get('class'), $registry->get('class'));
        self::assertSame(1, $registry->get('class')->create()->call);
        self::assertSame(1, $registry->get('class')->create()->call);
    }

    public function testStaticFactoryClassCanHaveAPrivateConstructor(): void
    {
        $registry = new FactoryRegistry();
        $registry->set('static', StaticFactory::class);
        self::assertSame(['label' => 'named'], $registry->get('static')->create(label: 'named')->arguments);
    }

    public function testInjectedFactoriesAreRegisteredAsObjects(): void
    {
        $registry = new FactoryRegistry();
        $registry->set('injected', new InjectedFactory('ready:'));
        self::assertSame('ready:a', $registry->get('injected')->create('a'));
        $registry->set('unconfigured', InjectedFactory::class);
        $this->expectException(\ArgumentCountError::class);
        $registry->get('unconfigured');
    }

    public function testMissingRegistrationUsesTheExactLocalException(): void
    {
        try {
            (new FactoryRegistry())->get('missing');
            self::fail('Expected missing registration.');
        } catch (OutOfBoundsException $exception) {
            self::assertSame(OutOfBoundsException::class, $exception::class);
            self::assertInstanceOf(FactoryExceptionInterface::class, $exception);
            self::assertInstanceOf(\OutOfBoundsException::class, $exception);
            self::assertStringContainsString('missing', $exception->getMessage());
        }
    }

    public function testInvalidReplacementLeavesThePreviousDefinitionUntouched(): void
    {
        $registry = new FactoryRegistry();
        $registry->set('entry', new ValueFactory(null));
        try {
            $registry->set('entry', \stdClass::class);
            self::fail('Expected invalid factory.');
        } catch (InvalidArgumentException $exception) {
            self::assertInstanceOf(FactoryExceptionInterface::class, $exception);
            self::assertNull($registry->get('entry')->create());
        }
    }

    public function testMissingFactoryClassRetainsTheOriginalReflectionError(): void
    {
        try {
            (new FactoryRegistry())->set('missing', 'NoSuchFactoryClass');
            self::fail('Expected missing factory class.');
        } catch (ReflectionException $exception) {
            self::assertSame(ReflectionException::class, $exception::class);
            self::assertInstanceOf(\ReflectionException::class, $exception->getPrevious());
        }
    }

    public function testAbstractStaticFactoryIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new FactoryRegistry())->set('abstract', AbstractStaticFactory::class);
    }

    public function testRegistryInstancesDoNotShareState(): void
    {
        $first = new FactoryRegistry();
        $first->set('id', new ValueFactory(1));
        self::assertFalse((new FactoryRegistry())->has('id'));
        self::assertTrue($first->remove('id'));
        self::assertFalse($first->remove('id'));
        $first->set('0', new ValueFactory(false));
        $first->set('', new ValueFactory(null));
        self::assertFalse($first->get('0')->create());
        self::assertNull($first->get('')->create());
        $first->clear();
        self::assertFalse($first->has('0'));
        self::assertFalse($first->has(''));
    }
}
