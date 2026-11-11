<?php

declare(strict_types=1);

namespace CoiSA\Factory;

interface ResolverInterface
{
    /** @param array<int|string, mixed> $arguments Positional or named construction arguments. */
    public function create(string $id, array $arguments = []): mixed;

    public function getFactory(string $id): FactoryInterface;
}
