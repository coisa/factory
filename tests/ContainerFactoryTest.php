<?php

declare(strict_types=1);

namespace CoiSA\Factory\Tests;

use CoiSA\Factory\ContainerFactory;
use CoiSA\Factory\Exception\ContainerException;
use CoiSA\Factory\Exception\ContainerNotFoundException;
use CoiSA\Factory\Exception\FactoryExceptionInterface;
use CoiSA\Factory\Exception\InvalidArgumentException;
use CoiSA\Factory\Factory;
use CoiSA\Factory\Tests\Fixture\ArrayContainer;
use CoiSA\Factory\Tests\Fixture\Product;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final class ContainerFactoryTest extends TestCase
{
    public function testContainerOwnsValuesAndLifecycleWithoutFactoryCaching(): void
    {
        $object = new \stdClass();
        $container = new ArrayContainer(['object' => $object, 'null' => null, 'scalar' => 42]);
        $factory = new Factory($container);
        self::assertSame($object, $factory->create('object'));
        self::assertSame($object, $factory->create('object'));
        self::assertNull($factory->create('null'));
        self::assertSame(42, $factory->create('scalar'));
        self::assertSame(4, $container->reads);
        self::assertEquals(new Product('fallback'), $factory->create(Product::class, ['fallback']));
    }

    public function testArgumentsCannotSilentlyDisappearIntoContainerLookup(): void
    {
        $container = new ArrayContainer(['id' => 'value']);
        try {
            (new ContainerFactory($container, 'id'))->create(ignored: 'no');
            self::fail('Expected unsupported arguments.');
        } catch (InvalidArgumentException) {
            self::assertSame(0, $container->reads);
        }
    }

    public function testPsrContainerFailureRetainsTheExactLocalTypeAndPrevious(): void
    {
        $error = new class ('dependency failed') extends \RuntimeException implements ContainerExceptionInterface {
        };
        try {
            (new Factory($this->failingContainer($error)))->create(Product::class);
            self::fail('Selected container strategy must not fall back.');
        } catch (ContainerException $exception) {
            self::assertSame(ContainerException::class, $exception::class);
            self::assertSame($error, $exception->getPrevious());
            self::assertInstanceOf(FactoryExceptionInterface::class, $exception);
            self::assertInstanceOf(ContainerExceptionInterface::class, $exception);
        }
    }

    public function testNotFoundPreservesThePsrNotFoundContract(): void
    {
        $error = new class ('missing') extends \RuntimeException implements NotFoundExceptionInterface {
        };
        try {
            (new ContainerFactory($this->failingContainer($error), 'missing'))->create();
            self::fail('Expected not found.');
        } catch (ContainerNotFoundException $exception) {
            self::assertSame(ContainerNotFoundException::class, $exception::class);
            self::assertInstanceOf(NotFoundExceptionInterface::class, $exception);
            self::assertSame($error, $exception->getPrevious());
        }
    }

    public function testNonPsrErrorsAreNotReclassified(): void
    {
        $error = new \LogicException('invalid container');
        try {
            (new Factory($this->failingContainer($error)))->create(Product::class);
            self::fail('Expected original failure.');
        } catch (\LogicException $caught) {
            self::assertSame($error, $caught);
        }
    }

    public function testContainerHasFailureAlsoPropagatesWithoutFallback(): void
    {
        $error = new \LogicException('has failed');
        $container = new class ($error) implements ContainerInterface {
            public function __construct(private \Throwable $error)
            {
            }

            public function has(string $id): bool
            {
                throw $this->error;
            }

            public function get(string $id): mixed
            {
                throw new \LogicException('get must not run');
            }
        };
        try {
            (new Factory($container))->create(Product::class, ['ignored']);
            self::fail('Expected has failure.');
        } catch (\LogicException $caught) {
            self::assertSame($error, $caught);
        }
    }

    private function failingContainer(\Throwable $error): ContainerInterface
    {
        return new class ($error) implements ContainerInterface {
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
    }
}
