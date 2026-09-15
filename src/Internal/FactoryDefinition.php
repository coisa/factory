<?php

declare(strict_types=1);

namespace CoiSA\Factory\Internal;

use CoiSA\Factory\AbstractFactoryFactory;
use CoiSA\Factory\AbstractFactoryInterface;
use CoiSA\Factory\Exception\InvalidArgumentException;
use CoiSA\Factory\Exception\ReflectionException;
use CoiSA\Factory\FactoryInterface;

/** @internal Shared validation for registry and attribute factory class definitions. */
final class FactoryDefinition
{
    /** @return \ReflectionClass<object> */
    public static function reflect(string $class): \ReflectionClass
    {
        try {
            $reflection = new \ReflectionClass($class);
        } catch (\ReflectionException $exception) {
            throw new ReflectionException($exception->getMessage(), 0, $exception);
        }

        if ($reflection->implementsInterface(AbstractFactoryInterface::class)) {
            if (!$reflection->getMethod('create')->isAbstract()) {
                return $reflection;
            }
        } elseif ($reflection->implementsInterface(FactoryInterface::class) && $reflection->isInstantiable()) {
            return $reflection;
        }

        throw new InvalidArgumentException(
            sprintf(
                'Factory "%s" must be an instantiable %s or a concrete static %s.',
                $class,
                FactoryInterface::class,
                AbstractFactoryInterface::class,
            ),
        );
    }

    public static function instantiate(string $class): FactoryInterface
    {
        $reflection = self::reflect($class);

        if ($reflection->implementsInterface(AbstractFactoryInterface::class)) {
            return new AbstractFactoryFactory($class);
        }

        /** @var FactoryInterface $factory */
        $factory = $reflection->newInstance();

        return $factory;
    }
}
