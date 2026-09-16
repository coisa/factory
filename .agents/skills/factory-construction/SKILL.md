---
name: factory-construction
description: Use CoiSA Factory 2.0 to introduce explicit construction seams, select factory strategies and lifetimes, or substitute interface-compatible test dependencies during legacy-to-DI refactoring.
---

# Factory construction

This skill describes the unreleased CoiSA Factory 2.0 source API, requiring PHP 8.3+. Confirm the consuming application's version before using it; published 1.x has a different API. In this source checkout, [installation](../../../docs/quickstart.md) and [migration](../../../docs/migration.md) explain evaluation and the breaking changes. The procedure and example below are self-contained.

## Choose the construction boundary

Use `CoiSA\Factory\Factory` where code deliberately needs deferred or repeated construction: for example, building a report with call-specific arguments, adapting a legacy static builder or supplying a fake renderer in a test. When a consumer only needs an already created dependency, inject that dependency directly.

`Factory::create(string $id, array $arguments = []): mixed` takes a positional list or a named argument map. It does not autowire missing dependencies or guarantee that the result has the identifier's type. Strategies implement `FactoryInterface::create(mixed ...$arguments): mixed`; calling a strategy directly uses variadic arguments, not the resolver's array convention.

Selection order is explicit registration → supplied PSR-11 container `has($id)` → `#[FactoryAttribute(...)]` → `AbstractFactoryInterface` static `create()` → reflection. Only absence advances the search. A selected factory's failure must remain visible.

| Need | Strategy and behavior |
| --- | --- |
| Ordinary constructor | Unregistered instantiable class, or `ReflectionClassFactory`; constructs a new object per call |
| Dependency assembly or legacy call | `CallableFactory($callback)`; invokes the callback with the supplied arguments each time |
| Shared instance or fixed fake | `ValueFactory($value)`; returns that same value and deliberately ignores construction arguments |
| Another identifier | `$factory->alias($id, $target)`; resolves the target through that resolver at call time and forwards arguments |
| Existing PSR-11 service | `new Factory(container: $container)` or `ContainerFactory($container, $id)`; the container owns lifetime and construction arguments are rejected |
| Legacy static factory | A class implementing `AbstractFactoryInterface`, optionally wrapped in `AbstractFactoryFactory`; only its explicit static `create()` is called |
| Class-selected strategy | `CoiSA\Factory\Attribute\FactoryAttribute` names a registered factory or a factory class requiring no constructor arguments; it does not name an arbitrary container service |

## Replace a typed dependency

Run this example from an application or checkout with the 2.0 package installed and `vendor/autoload.php` available:

```php
<?php

declare(strict_types=1);

require 'vendor/autoload.php';

use CoiSA\Factory\CallableFactory;
use CoiSA\Factory\Factory;
use CoiSA\Factory\ValueFactory;

interface Renderer
{
    public function render(string $text): string;
}

final readonly class PlainRenderer implements Renderer
{
    public function __construct(private string $prefix)
    {
    }

    public function render(string $text): string
    {
        return $this->prefix . $text;
    }
}

$factory = new Factory();
$factory->set(Renderer::class, new CallableFactory(
    static fn (string $prefix): Renderer => new PlainRenderer($prefix),
));
$real = $factory->create(Renderer::class, ['prefix' => 'Report: ']);

$factory->set(Renderer::class, new ValueFactory(new class implements Renderer {
    public function render(string $text): string
    {
        return 'Fixture: ' . $text;
    }
}));
$fake = $factory->create(Renderer::class);

echo $real->render('weekly'), "\n";
echo $fake->render('weekly'), "\n";
```

Expected output is `Report: weekly` followed by `Fixture: weekly`. Both objects satisfy `Renderer`; replacing a registration affects subsequent calls through this resolver, not the object already created. Inject the chosen `Renderer` into a consumer that does not need to construct one itself.

## Preserve lifecycle and error behavior

Each `new Factory()` owns a fresh registry unless one is explicitly supplied via `registry:`. `set($id, $factoryObject)` reuses the strategy object; registering an instance-factory class name creates a fresh strategy on every lookup. Register an already configured object if its constructor needs dependencies. The resolver caches neither products nor automatically selected strategies.

Identifiers are exact, case-sensitive registration keys. `has()` checks only explicit registrations. `remove()` and `clear()` affect the supplied registry, including all resolvers intentionally sharing it; they do not clear a container. An alias observes later replacement of its target. An attribute is not inherited or repeatable; an explicitly retained `ReflectionAttributeFactory` fixes its delegate when that adapter is constructed.

Resolver `create()` tracks alias and recursive resolution paths, releases the path after success or failure and separates it per Fiber. `getFactory()` selects a strategy without producing its value and can itself throw; invoking that strategy directly bypasses the resolver's outer cycle tracking. Fiber isolation covers bookkeeping, not concurrency safety or disposal of user factories and container services.

Preserve user constructor/callback/static-factory errors and native PHP argument/type errors. Container `get()` exceptions implementing `NotFoundExceptionInterface` or `ContainerExceptionInterface` are wrapped in local PSR-compatible exceptions retaining `getPrevious()`; not-found errors retain their PSR interface. Other `get()` errors and a throwing container `has()` propagate unchanged. Do not catch every error to silently retry another strategy.

## Own test doubles

A fake must satisfy the consumer's actual type contract. Factory does not intercept arbitrary `new` expressions or static calls. A final class can be constructed but cannot be subclassed by a conventional double; an unrelated fake cannot satisfy its concrete type. Internal classes retain their individual PHP restrictions.

If the application uses Prophecy, install `phpspec/prophecy` as its optional test dependency through the application's normal dependency workflow. The core runs without it.

- `ProphesizeFactory($prophet, $classOrInterface, $configure)` creates a fresh double per call using a caller-owned `Prophecy\Prophet`.
- `ProphecyFactory($objectProphecy, $configure)` reuses a caller/framework-owned `ObjectProphecy` and its revealed double.
- Either optional configure callback receives `(ObjectProphecy $double, array $arguments)` with the original argument keys. The caller/framework must verify predictions at the test's verification point, such as `$prophet->checkPredictions()`; returning or destroying a factory does not verify them.

For an integration change, test the observable consumer result, a compatible replacement and any chosen lifecycle or failure behavior. In a Factory source checkout, the [executable examples](../../../docs/examples.md) and [testing guide](../../../docs/testing.md) provide working cases; `composer --no-plugins examples` verifies their exact outputs. Use the [API reference](../../../docs/api.md) when selecting an adapter or handling a specific exception.
