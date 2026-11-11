# Strategies & lifetimes

A resolver selects one strategy for a requested identifier. Registry keys are exact and case-sensitive; use canonical class names consistently. The resolver forwards identifiers unchanged to a supplied container, whose own rules determine identifier matching and aliases.

## Resolution order

| Priority | Condition | Selected behavior |
| --- | --- | --- |
| 1 | An explicit registry entry exists | Use its factory object or instantiate its factory class |
| 2 | The supplied PSR-11 container reports `has($id)` | Read `get($id)` from that container |
| 3 | The class has `#[FactoryAttribute(...)]` | Use the named metadata factory |
| 4 | The class implements `AbstractFactoryInterface` | Call its static `create(...$arguments)` |
| 5 | The class is instantiable | Call its constructor through reflection |

Absence advances the search. Failure after selecting a strategy does not. A container error, malformed attribute, throwing factory constructor or throwing callback remains visible. Factory delegates autowiring to the supplied container; its own reflection and metadata strategies do not inject constructor dependencies.

## Choose the smallest seam

| Strategy | Configuration | Produced value / lifetime |
| --- | --- | --- |
| `ReflectionClassFactory` | An instantiable object or class name | A new instance on each call |
| `CallableFactory` | A PHP callable | Calls it every time; the callback owns its result |
| `ValueFactory` | Any value, including `null` | Returns the same value; deliberately ignores construction arguments |
| `ContainerFactory` | PSR-11 container and identifier | Calls `get()` every time; the container owns lifetime |
| `AbstractFactoryFactory` | A class implementing the static factory contract | Invokes that class's `create()`; it owns lifetime |
| `AliasFactory` | A resolver and target identifier | Resolves the target at call time and forwards arguments |
| `ReflectionAttributeFactory` | An attributed class and optional registry | Delegates to the metadata-selected factory |
| `ProphecyFactory` | Caller-provided `ObjectProphecy` | Reuses that prophecy and revealed double |
| `ProphesizeFactory` | Caller-owned `Prophet`, type and optional callback | A fresh double for each call |

The resolver caches no products or automatically selected strategies. Use `ValueFactory` for an explicitly shared instance, or let a container/callback implement application-specific lifetimes.

## Registrations

```php
$factory->set('builder', new MyFactory($dependency)); // reuse this factory object
$factory->set('fresh', MyNoArgumentFactory::class);   // new factory on every lookup
$factory->alias('public.name', 'builder');
```

A registered factory object's state survives calls. A registered factory class must implement `FactoryInterface` with an accessible constructor, or a concrete static `AbstractFactoryInterface::create()`. Class definitions are validated when registered and instantiated when selected. If a constructor needs dependencies, register an already configured object, including one created by your container. Resolving a factory's class through a container produces the factory object; register it under a product identifier to invoke it for that product. A constructor failure propagates.

Aliases see later replacement of their target. Self-aliases, alias loops and recursive `create()` calls throw `CircularDependencyException` with the resolution path. The active path is released after success or failure and is separate for each Fiber. That protects resolution bookkeeping; it does not make user factories or containers concurrency-safe.

## Attributes

```php
use CoiSA\Factory\Attribute\FactoryAttribute;

#[FactoryAttribute('report.builder')]
final class Report
{
    // ...
}

$factory->set('report.builder', new ReportFactory($dependency));
$report = $factory->create(Report::class, ['title' => 'Daily report']);
```

The attribute names a registered strategy in this resolver's registry, or a factory class constructible with no arguments. It is not an arbitrary container identifier or implicit dependency-injection instruction. A registered strategy wins over a factory class with the same name. The attribute is not repeatable, inherited, or an autoload alias. If you keep a `ReflectionAttributeFactory` object yourself, its selected delegate is fixed at that adapter's construction.

## PSR-11 arguments

PSR-11 `get($id)` accepts no construction arguments. Passing any to `ContainerFactory` throws `InvalidArgumentException` before lookup. Register a `CallableFactory` explicitly when you need to resolve dependencies from a container and then construct with call-specific arguments.

## Container changes and the legacy bridge

`$factory->setContainer($container)` changes future selections on the same resolver; pass `null` to detach the container. Registrations and aliases survive either operation. A selected container remains fixed for that in-flight selection. Holding a `ContainerFactory` returned earlier keeps its original container, while holding an alias keeps a dynamic route through the resolver.

`LegacyFactory` exposes this resolver through an opt-in static entrypoint for sequential legacy work. Its `reset()` clears the shared registry and detaches the container in place. Ordinary `new Factory()` instances remain isolated by default. Neither operation reinjects products, disposes resources or clears a container's cached services. The [legacy adoption guide](legacy-adoption.md) shows configured factories, lazy container-aware callbacks and incremental migration to directly injected dependencies.
