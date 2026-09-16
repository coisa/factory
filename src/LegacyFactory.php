<?php

declare(strict_types=1);

namespace CoiSA\Factory;

use Psr\Container\ContainerInterface;

/** Opt-in static entrypoint for sequential legacy code migrating toward instance injection. */
final class LegacyFactory
{
    private static ?Factory $resolver = null;

    private function __construct()
    {
    }

    /** The identity remains stable across container changes and reset(). */
    public static function resolver(): Factory
    {
        return self::$resolver ??= new Factory();
    }

    public static function setContainer(?ContainerInterface $container): void
    {
        self::resolver()->setContainer($container);
    }

    /** @param array<int|string, mixed> $arguments Positional or named construction arguments. */
    public static function create(string $id, array $arguments = []): mixed
    {
        return self::resolver()->create($id, $arguments);
    }

    public static function getFactory(string $id): FactoryInterface
    {
        return self::resolver()->getFactory($id);
    }

    /** Clear bridge registrations and detach its container; do not dispose existing objects. */
    public static function reset(): void
    {
        self::resolver()->clear();
        self::resolver()->setContainer(null);
    }
}
