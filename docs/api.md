# API & errors

All public classes live under `CoiSA\Factory`. The registry is under `CoiSA\Factory\Registry`; error classes are under `CoiSA\Factory\Exception`.

## Factory and ResolverInterface

```php
new Factory(
    ?Psr\Container\ContainerInterface $container = null,
    FactoryRegistryInterface $registry = new FactoryRegistry(),
);

$factory->create(string $id, array $arguments = []): mixed;
$factory->getFactory(string $id): FactoryInterface;
$factory->setContainer(?Psr\Container\ContainerInterface $container): void;
$factory->set(string $id, FactoryInterface|string $factory): void;
$factory->has(string $id): bool;
$factory->remove(string $id): bool;
$factory->clear(): void;
$factory->alias(string $id, string $target): void;
```

These are signature summaries, not a runnable PHP program. `ResolverInterface` contains `create()` and `getFactory()`. A factory strategy implements `FactoryInterface::create(mixed ...$arguments): mixed`. A legacy static factory implements `AbstractFactoryInterface::create(mixed ...$arguments): mixed`.

`has()` checks explicit registrations only. It does not query the container or claim that a class can be constructed. `remove()` returns whether a registration existed. `clear()` removes registrations and aliases from the supplied registry, not the container. Sharing a registry between resolvers is explicit; changes then affect all those resolvers.

`getFactory()` returns the selected strategy without producing a value. Selecting it can construct a factory class and can therefore throw. Calling that returned strategy directly follows the same strategy contract; call `create()` on the resolver when you want its complete cycle tracking.

`setContainer()` installs, replaces or detaches the container for future selections. The resolver, its registrations and existing aliases stay intact. Each in-flight selection uses the container it consulted, even if `has()` changes configuration or yields. A `ContainerFactory` already returned by `getFactory()` keeps that selected container; a retained alias continues resolving its target dynamically. Existing products and configured factories retain their dependencies. No container services are cleared or reinjected.

## LegacyFactory

```php
LegacyFactory::resolver(): Factory;
LegacyFactory::setContainer(?Psr\Container\ContainerInterface $container): void;
LegacyFactory::create(string $id, array $arguments = []): mixed;
LegacyFactory::getFactory(string $id): FactoryInterface;
LegacyFactory::reset(): void;
```

This opt-in static bridge lazily creates one shared `Factory`. Use `resolver()` for registrations, aliases or gradual resolver injection. `create()` and `getFactory()` use the same selection and error contracts as the instance API. Calls can precede container installation when their strategy can already construct the value.

`reset()` calls `clear()` and detaches the container on the **same resolver**. References already injected elsewhere see the cleared registry and disconnected container. Captured strategies and products retain their own lifetimes; reset does not revoke them or purge external container caches. Use `finally` at sequential job/test boundaries. The bridge is not scoped by request or Fiber; use separately owned instances for concurrent work. See the [legacy adoption guide](legacy-adoption.md) for working bootstrap and migration examples.

## FactoryRegistryInterface

The registry exposes `set()`, `has()`, `get()`, `remove()` and `clear()` with the same signatures as the resolver's registration methods; `get(string $id): FactoryInterface` is a strict lookup. There is no fallback in `get()`. The default `FactoryRegistry` is per instance.

## Adapter constructors

| Class | Constructor arguments |
| --- | --- |
| `ReflectionClassFactory` | `object|string $objectOrClassName` |
| `CallableFactory` | `callable $callback` |
| `ValueFactory` | `mixed $value` |
| `AliasFactory` | `ResolverInterface $resolver, string $target` |
| `ContainerFactory` | `ContainerInterface $container, string $id` |
| `AbstractFactoryFactory` | `string $abstractFactory` |
| `ReflectionAttributeFactory` | `object|string $objectOrClassName, ?FactoryRegistryInterface $registry = null` |
| `ProphecyFactory` | `ObjectProphecy $objectProphecy, ?callable $configure = null` |
| `ProphesizeFactory` | `Prophet $prophet, string $classOrInterface, ?callable $configure = null` |

Every adapter implements `create(mixed ...$arguments): mixed` (some narrow their return to `object`). Prophecy configuration callbacks receive `(ObjectProphecy $double, array $arguments)`, keeping positional or named argument keys. Prediction verification belongs to the caller. `FactoryAttribute` accepts one `string $factory` and exposes it as a readonly property.

## Error contracts

| Situation | Error |
| --- | --- |
| Missing registry key | Local `OutOfBoundsException` |
| Missing class or non-instantiable reflection target | Local `ReflectionException`; original reflection failure retained when present |
| Definition does not implement the required factory contract | Local `InvalidArgumentException` |
| Recursive resolver call / alias loop | Local `CircularDependencyException` with path |
| PSR-11 `get()` throws `NotFoundExceptionInterface` | Local `ContainerNotFoundException`, also implementing the PSR interface |
| PSR-11 `get()` throws another `ContainerExceptionInterface` | Local `ContainerException`, also implementing `ContainerExceptionInterface` |
| Arguments passed to a PSR-11 lookup | Local `InvalidArgumentException` |
| Invalid callable, type mismatch or missing PHP argument | Native PHP `TypeError` / `ArgumentCountError` |
| Malformed or repeated attribute | Native PHP attribute error |
| User constructor, static factory, callback, container `has()` or non-PSR container `get()` throws | Original error, unchanged |

All local errors implement `FactoryExceptionInterface`, which extends `Throwable`. Local reflection/out-of-bounds/invalid-argument errors also extend their corresponding native PHP error classes. Container wrappers preserve `getPrevious()`. No exception depends on `coisa/exceptions` or its inherited static creation helpers.

Factory-created values are intentionally `mixed`: a scalar factory or a fake registered under a concrete class name is allowed. The consumer remains responsible for its type contract; PHP will reject an incompatible concrete-typed argument. No output is silently coerced into the requested class.

The `Internal` namespace is an implementation detail and has no public compatibility promise.
