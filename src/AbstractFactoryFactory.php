<?php

declare(strict_types=1);

namespace CoiSA\Factory;

use CoiSA\Factory\Exception\InvalidArgumentException;
use CoiSA\Factory\Exception\ReflectionException;

/** Calls only the explicit AbstractFactoryInterface::create() construction seam. */
final readonly class AbstractFactoryFactory implements FactoryInterface
{
    public function __construct(private string $abstractFactory)
    {
        try {
            $reflection = new \ReflectionClass($abstractFactory);
        } catch (\ReflectionException $exception) {
            throw new ReflectionException($exception->getMessage(), 0, $exception);
        }

        if (
            !$reflection->implementsInterface(AbstractFactoryInterface::class)
            || $reflection->getMethod('create')->isAbstract()
        ) {
            throw new InvalidArgumentException(
                sprintf(
                    '"%s" must implement a concrete %s::create().',
                    $abstractFactory,
                    AbstractFactoryInterface::class,
                ),
            );
        }
    }

    public function create(mixed ...$arguments): mixed
    {
        return ($this->abstractFactory)::create(...$arguments);
    }
}
