# Migrate from 1.x

Version 2.0 is a breaking API modernization. Move incrementally at application composition boundaries and keep application tests running. No automatic codemod, downstream compatibility claim or published 2.0 tag is included.

## Compatibility floor

Use PHP 8.3+. The 2.0 CI matrix covers 8.3–8.5. PHP 7.4, older PHP 8 releases, PSR-11 v1 and Doctrine annotations are no longer supported. The old PHP 7.4 platform override and Composer autoload alias file are removed.

## Replace global calls

Before:

```php
AbstractFactory::setFactory(Service::class, new ValueFactory($fake));
$service = AbstractFactory::create(Service::class, 'value');
```

After:

```php
$factory = new CoiSA\Factory\Factory();
$factory->set(Service::class, new CoiSA\Factory\ValueFactory($fake));
$service = $factory->create(Service::class, ['value']);
```

Pass that resolver or the specific `FactoryInterface` into the code that needs deferred construction. Prefer passing the actual dependency when construction is not the consumer's responsibility.

| 1.x surface | 2.0 replacement |
| --- | --- |
| `AbstractFactory` global facade | An explicitly owned `Factory` instance |
| `FactoryAbstractFactory` global facade | `$factory->getFactory($id)` |
| `FactoryFactory` selector | `$factory->getFactory($id)` |
| Static `FactoryRegistry::set/get/has` | Instance `FactoryRegistry` methods |
| `setFactory($id, $factory)` | `$factory->set($id, $factory)` |
| Global `setContainer($container)` | `new Factory($container)` |
| `new AliasFactory($target)` | `$factory->alias($id, $target)` or `new AliasFactory($resolver, $target)` |
| Unspecified `create()` signature | `create(mixed ...$arguments): mixed` on factory interfaces |
| Global `create($class, ...$arguments)` | `$factory->create($id, $arguments)` with a list/map |
| `Attribute\Factory` Composer alias | Real `Attribute\FactoryAttribute` class |
| Doctrine annotation factories / annotation aliases | PHP `#[FactoryAttribute(...)]` |
| `FactoryAttribute::getFactory()` | Immutable factory identifier; resolver selects the delegate |
| `new ProphesizeFactory($type, $configure)` | `new ProphesizeFactory($prophet, $type, $configure)` |
| Inherited `coisa/exceptions` helpers | Local native exception constructors and marker interface |

The removed names are not shipped as compatibility shims. `AbstractFactoryInterface` and its `AbstractFactoryFactory` adapter remain, with the explicit variadic/mixed signature, for actual static factory methods.

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

Attributes accept a factory identifier or factory class name, not a factory object literal. Register injected metadata factories in the resolver's registry. Metadata selection does not resolve those factory dependencies from the container automatically.

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

There is no global reset step because there is no library-owned global registry. Review application-level singletons or static storage that might reintroduce that lifetime.
