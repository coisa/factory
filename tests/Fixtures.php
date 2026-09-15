<?php

declare(strict_types=1);

namespace CoiSA\Factory\Tests\Fixture;

use CoiSA\Factory\AbstractFactoryInterface;
use CoiSA\Factory\Attribute\FactoryAttribute;
use CoiSA\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

interface Service
{
    public function run(): string;
}

final class RealService implements Service
{
    public function run(): string
    {
        return 'real';
    }

    public static function direct(): string
    {
        return 'static-real';
    }
}

final readonly class Product
{
    public function __construct(public string $id, public string $label = 'default')
    {
    }
}

final class NoConstructor
{
}

final class CountingFactory implements FactoryInterface
{
    private int $calls = 0;

    public function create(mixed ...$arguments): object
    {
        return (object) ['call' => ++$this->calls, 'arguments' => $arguments];
    }
}

final class InjectedFactory implements FactoryInterface
{
    public function __construct(private string $prefix)
    {
    }

    public function create(mixed ...$arguments): string
    {
        return $this->prefix . implode(',', $arguments);
    }
}

final class ExplodingFactory implements FactoryInterface
{
    public function __construct()
    {
        throw new \LogicException('factory construction failed');
    }

    public function create(mixed ...$arguments): mixed
    {
        throw new \LogicException('unreachable');
    }
}

final class StaticFactory implements AbstractFactoryInterface
{
    private function __construct()
    {
    }

    public static function create(mixed ...$arguments): object
    {
        return (object) ['strategy' => 'static', 'arguments' => $arguments];
    }
}

abstract class AbstractStaticFactory implements AbstractFactoryInterface
{
    abstract public static function create(mixed ...$arguments): mixed;
}

#[FactoryAttribute(CountingFactory::class)]
final class AttributedProduct
{
}

#[FactoryAttribute(CountingFactory::class)]
final class AttributeAndStatic implements AbstractFactoryInterface
{
    public static function create(mixed ...$arguments): string
    {
        return 'static';
    }
}

#[FactoryAttribute(ExplodingFactory::class)]
final class BrokenMetadata
{
    public static int $fallbackConstructions = 0;

    public function __construct()
    {
        ++self::$fallbackConstructions;
    }
}

#[FactoryAttribute(unknown: 'factory')]
final class InvalidMetadata
{
}

#[FactoryAttribute(CountingFactory::class)]
#[FactoryAttribute(CountingFactory::class)]
final class RepeatedMetadata
{
}

#[FactoryAttribute('injected.builder')]
final class InjectedMetadata
{
}

final class ArrayContainer implements ContainerInterface
{
    public int $reads = 0;

    /** @param array<string, mixed> $values */
    public function __construct(private array $values)
    {
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->values);
    }

    public function get(string $id): mixed
    {
        ++$this->reads;
        return $this->values[$id];
    }
}
