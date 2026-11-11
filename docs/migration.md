# Migrate from 1.x

Version 2.0 is a breaking API modernization. Move incrementally at application composition boundaries and keep application tests running. No automatic codemod, downstream compatibility claim or published 2.0 tag is included.

## Compatibility floor

Use PHP 8.3+. The 2.0 CI matrix covers 8.3–8.5. PHP 7.4, older PHP 8 releases, PSR-11 v1 and Doctrine annotations are no longer supported. The old PHP 7.4 platform override and Composer autoload alias file are removed.

## Keep a static entrypoint during incremental migration

Before:

```php
AbstractFactory::setFactory(Service::class, new ValueFactory($fake));
$service = AbstractFactory::create(Service::class, 'value');
```

After, using the opt-in bridge while callers still rely on static methods:

```php
$factory = CoiSA\Factory\LegacyFactory::resolver();
$factory->set(Service::class, new CoiSA\Factory\ValueFactory($fake));
$service = CoiSA\Factory\LegacyFactory::create(Service::class, ['value']);
```

When application bootstrap obtains a PSR-11 container, call `LegacyFactory::setContainer($container)`. The next selection uses it after registry lookup, preserving previous aliases, overrides and injected resolver references. A container capable of autowiring can construct ordinary classes and factory objects without attributes. The [legacy adoption guide](legacy-adoption.md) supplies the two-line bootstrap and executable PHP-DI example.

Move each consumer toward constructor injection when its call site can change: inject that resolver first, then an application-owned `new Factory($container)` or a specific `FactoryInterface`. Prefer passing the actual dependency when construction is not the consumer's responsibility. `LegacyFactory` shares state for sequential use; it is not the default state of a new resolver.

| 1.x surface | 2.0 replacement |
| --- | --- |
| `AbstractFactory` global facade | Opt-in `LegacyFactory::create($id, $arguments)` during migration, or an explicitly owned `Factory` |
| `FactoryAbstractFactory` global facade | `LegacyFactory::getFactory($id)` or `$factory->getFactory($id)` |
| `FactoryFactory` selector | `getFactory($id)` on the bridge or owned resolver |
| Static `FactoryRegistry::set/get/has` | Instance `FactoryRegistry` methods |
| `setFactory($id, $factory)` | `$factory->set($id, $factory)` |
| Global `setContainer($container)` | `LegacyFactory::setContainer($container)` or `$factory->setContainer($container)` on a stable resolver |
| `new AliasFactory($target)` | `$factory->alias($id, $target)` or `new AliasFactory($resolver, $target)` |
| Unspecified `create()` signature | `create(mixed ...$arguments): mixed` on factory interfaces |
| Global `create($class, ...$arguments)` | `LegacyFactory::create($id, $arguments)` or `$factory->create($id, $arguments)` with a list/map |
| `Attribute\Factory` Composer alias | Real `Attribute\FactoryAttribute` class |
| Doctrine annotation factories / annotation aliases | PHP `#[FactoryAttribute(...)]` |
| `FactoryAttribute::getFactory()` | Immutable factory identifier; resolver selects the delegate |
| `new ProphesizeFactory($type, $configure)` | `new ProphesizeFactory($prophet, $type, $configure)` |
| Inherited `coisa/exceptions` helpers | Local native exception constructors and marker interface |

The removed names are not shipped as compatibility shims. `AbstractFactoryInterface` and its `AbstractFactoryFactory` adapter remain, with the explicit variadic/mixed signature, for actual static factory methods.

`create()` returns a product; `getFactory()` returns the selected strategy. Resolving a class that is itself an instance factory returns that factory object. Register it under the product identifier, or use a callback that resolves it and calls its `create()`, when the next step should produce that product.

## Adjust custom strategies

```php
final class MyFactory implements CoiSA\Factory\FactoryInterface
{
    public function create(mixed ...$arguments): mixed
    {
        return new MyService(...$arguments);
    }
}
```

Do not recover arguments with `func_get_args()`. Variadics preserve named keys. Register an instantiated factory when its constructor requires injected dependencies. String registrations still create a fresh no-argument factory on each lookup; object registrations reuse their supplied object.

Attributes accept a factory identifier or factory class name, not a factory object literal. Register injected metadata factories in the resolver's registry, for example with `$factory->set(ReportFactory::class, $factory->create(ReportFactory::class))` after container installation. Metadata selection does not resolve those factory dependencies from the container automatically. The same explicit registration works without a product attribute when registered under the product identifier. Use the adoption guide's lazy callback if the factory must follow future container changes.

## Review behavior changes

- Selected container/metadata/static-factory errors propagate instead of falling back.
- Container factories reject construction arguments; `ValueFactory` intentionally ignores them.
- Reflection uses normal instantiation and never bypasses constructors.
- Invalid callables and invalid PHP arguments use native PHP errors.
- Factory library errors now reliably have their local exception class.
- Container not-found wrappers preserve the PSR-11 not-found interface and the previous exception.
- Aliases and recursive resolver calls detect cycles.
- Prophecy prediction verification is explicitly owned by the test.
- A fresh resolver has fresh state. Shared registries must be supplied intentionally.
- Container changes affect future selections; a captured container strategy and previously created product retain their existing container or dependencies.

When using the opt-in bridge, call `LegacyFactory::reset()` in `finally` at sequential job/test boundaries. Reset clears its registry and detaches the container on the same resolver, including references already injected elsewhere. It does not purge the container's cached services or dispose products. Use a new context-specific container where necessary and independently owned resolvers for concurrent requests.
