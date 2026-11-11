<?php

declare(strict_types=1);

namespace CoiSA\Factory;

/** Always returns the same value; construction arguments are deliberately ignored. */
final readonly class ValueFactory implements FactoryInterface
{
    public function __construct(private mixed $value)
    {
    }

    public function create(mixed ...$arguments): mixed
    {
        return $this->value;
    }
}
