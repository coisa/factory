---
name: coisa-factory
description: CoiSA Factory (coisa/factory) usage for explicit construction seams, opt-in static-to-DI migration, factory lifetimes and interface-compatible test dependencies in the 2.0 source API.
---

# CoiSA Factory

Public source: [coisa/factory](https://github.com/coisa/factory). This skill ships with the library and describes its own construction APIs.

This skill describes the unreleased CoiSA Factory 2.0 source API, requiring PHP 8.3+. Confirm the consuming application's version before using it; published 1.x has a different API. In the matching CoiSA Factory checkout, `docs/quickstart.md` and `docs/migration.md` explain evaluation and the breaking changes. These paths belong to the library, not the consuming application's own documentation. The procedure and example below are self-contained.

## Choose the construction boundary

Use `CoiSA\Factory\Factory` where code deliberately needs deferred or repeated construction: for example, building a report with call-specific arguments, adapting a legacy static builder or supplying a fake renderer in a test. When a consumer only needs an already created dependency, inject that dependency directly.

`Factory::create(string $id, array $arguments = []): mixed` takes a positional list or a named argument map. Its own reflection and metadata strategies do not autowire missing dependencies; a supplied container can. It does not guarantee that the result has the identifier's type. Strategies implement `FactoryInterface::create(mixed ...$arguments): mixed`; calling a strategy directly uses variadic arguments, not the resolver's array convention.

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

## Connect a legacy entrypoint to DI

For sequential legacy code, opt into `CoiSA\Factory\LegacyFactory`. Given an existing PSR-11 container and service, bootstrap takes two lines:

```php
CoiSA\Factory\LegacyFactory::setContainer($container);
$service = CoiSA\Factory\LegacyFactory::create(Service::class);
```

Calls can precede container installation: no-argument classes use reflection and explicit strategies work normally. `LegacyFactory::resolver()` returns one stable `Factory`. Register an interface-compatible fake with its `set()` or add `alias()` before bootstrap; later container installation preserves both. The next selection follows registry → container → the remaining strategies. The core depends only on PSR-11; an application chooses its container. The source checkout tests PHP-DI autowiring with attributes disabled.

Resolving an ordinary factory class through that container returns the factory object. To create products with call-specific arguments, register the resulting `FactoryInterface` object under a product identifier. String factory registrations instead construct a fresh no-argument factory on each lookup. A configured factory keeps its injected dependencies. If it must follow a future container change, register a `CallableFactory` whose callback resolves the builder through the stable resolver and invokes its `create(...$arguments)`; keep builder and product identifiers different to avoid recursion. Container `get()` itself accepts no construction arguments.

For a hardcoded static collaborator, define an application interface and an adapter whose instance method calls that existing static method. Register a callback that constructs the adapter. Change the selected legacy caller to obtain the interface through `LegacyFactory::create()`, then substitute a compatible fake in tests. Unchanged direct static calls continue running normally. Migrate that caller to an injected `ResolverInterface`, then an independently owned `Factory`, and finally the dependency itself when it needs no deferred construction. In the matching library checkout, `docs/legacy-adoption.md`, `examples/legacy-bridge.php` and `examples/legacy-to-di.php` provide the complete PHP-DI and static-adapter walkthrough.

## Preserve lifecycle and error behavior

Each `new Factory()` owns a fresh registry unless one is explicitly supplied via `registry:`. `set($id, $factoryObject)` reuses the strategy object; registering an instance-factory class name creates a fresh strategy on every lookup. Register an already configured object if its constructor needs dependencies. The resolver caches neither products nor automatically selected strategies.

Identifiers are exact, case-sensitive registration keys. `has()` checks only explicit registrations. `remove()` and `clear()` affect the supplied registry, including all resolvers intentionally sharing it; they do not clear a container. An alias observes later replacement of its target. An attribute is not inherited or repeatable; an explicitly retained `ReflectionAttributeFactory` fixes its delegate when that adapter is constructed.

`Factory::setContainer(?ContainerInterface $container)` installs, swaps or detaches the container without replacing the resolver or its registry. Each selection keeps the container it consulted even if `has()` yields or reconfigures it. A captured `ContainerFactory` keeps that old container, while a captured alias still resolves its target through the stable resolver. Existing products retain their own state and dependencies.

Call `LegacyFactory::reset()` before and in `finally` after a sequential job or test. Reset clears the registry and detaches the container on the same resolver; injected resolver references see it. It does not revoke captured strategies, reinject products, dispose resources or clear container caches. Reusing the same PHP-DI container also reuses its cached services; construct a new contextual container when needed. The bridge is not Fiber/request scoped. Use separately owned resolvers and appropriately scoped containers for concurrent work.

Resolver `create()` tracks alias and recursive resolution paths, releases the path after success or failure and separates it per Fiber. `getFactory()` selects a strategy without producing its value and can itself throw; invoking that strategy directly bypasses the resolver's outer cycle tracking. Fiber isolation covers bookkeeping, not concurrency safety or disposal of user factories and container services.

Preserve user constructor/callback/static-factory errors and native PHP argument/type errors. Container `get()` exceptions implementing `NotFoundExceptionInterface` or `ContainerExceptionInterface` are wrapped in local PSR-compatible exceptions retaining `getPrevious()`; not-found errors retain their PSR interface. Other `get()` errors and a throwing container `has()` propagate unchanged. Do not catch every error to silently retry another strategy.

## Own test doubles

A fake must satisfy the consumer's actual type contract. Factory does not intercept arbitrary `new` expressions or static calls. A final class can be constructed but cannot be subclassed by a conventional double; an unrelated fake cannot satisfy its concrete type. Internal classes retain their individual PHP restrictions.

If the application uses Prophecy, install `phpspec/prophecy` as its optional test dependency through the application's normal dependency workflow. The core runs without it.

- `ProphesizeFactory($prophet, $classOrInterface, $configure)` creates a fresh double per call using a caller-owned `Prophecy\Prophet`.
- `ProphecyFactory($objectProphecy, $configure)` reuses a caller/framework-owned `ObjectProphecy` and its revealed double.
- Either optional configure callback receives `(ObjectProphecy $double, array $arguments)` with the original argument keys. The caller/framework must verify predictions at the test's verification point, such as `$prophet->checkPredictions()`; returning or destroying a factory does not verify them.

For an integration change, test the observable consumer result, a compatible replacement and any chosen lifecycle or failure behavior. In the matching CoiSA Factory source checkout, `docs/examples.md` and `docs/testing.md` provide working cases; `composer --no-plugins examples` verifies their exact outputs. Use that checkout's `docs/api.md` when selecting an adapter or handling a specific exception.
