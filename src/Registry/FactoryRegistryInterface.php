<?php

declare(strict_types=1);

namespace CoiSA\Factory\Registry;

use CoiSA\Factory\FactoryInterface;

interface FactoryRegistryInterface
{
    public function set(string $id, FactoryInterface|string $factory): void;

    public function has(string $id): bool;

    public function get(string $id): FactoryInterface;

    public function remove(string $id): bool;

    public function clear(): void;
}
