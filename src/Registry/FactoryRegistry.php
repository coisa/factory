<?php

declare(strict_types=1);

namespace CoiSA\Factory\Registry;

use CoiSA\Factory\Exception\OutOfBoundsException;
use CoiSA\Factory\FactoryInterface;
use CoiSA\Factory\Internal\FactoryDefinition;

/** Objects are reused; class definitions create a fresh factory on every lookup. */
final class FactoryRegistry implements FactoryRegistryInterface
{
    /** @var array<string, FactoryInterface|string> */
    private array $factories = [];

    public function set(string $id, FactoryInterface|string $factory): void
    {
        if (is_string($factory)) {
            FactoryDefinition::reflect($factory);
        }

        $this->factories[$id] = $factory;
    }

    public function has(string $id): bool
    {
        return array_key_exists($id, $this->factories);
    }

    public function get(string $id): FactoryInterface
    {
        if (!$this->has($id)) {
            throw new OutOfBoundsException(sprintf('No factory is registered for "%s".', $id));
        }

        $factory = $this->factories[$id];

        return is_string($factory) ? FactoryDefinition::instantiate($factory) : $factory;
    }

    public function remove(string $id): bool
    {
        $exists = $this->has($id);
        unset($this->factories[$id]);

        return $exists;
    }

    public function clear(): void
    {
        $this->factories = [];
    }
}
