# Testing & limits

The useful promise is **replace construction at an explicit factory boundary**. The tests prove both the replacement and the calls that remain unchanged.

## Own the seam

If a consumer accepts an interface, register an interface-compatible fake with `ValueFactory` or `CallableFactory`. Prefer injecting the finished dependency when creation is no longer the consumer's responsibility. Use a factory inside a consumer when it deliberately needs deferred or repeated construction.

A replacement affects only calls that use that resolver or injected factory. A direct `new Service()` expression and a direct `Service::connect()` call still execute normally. Moving the call into a callback creates a seam; the library does not rewrite PHP or replace arbitrary classes through Composer.

A final class can be constructed normally. It cannot be subclassed by a conventional double. An unrelated fake also cannot satisfy a parameter typed as that concrete final class. Internal PHP classes have individual instantiation and inheritance restrictions; a successful `DateTimeImmutable` example is not proof that every internal class is constructible or mockable.

## Prophecy verification

Install `phpspec/prophecy` in the consuming application's test dependencies to use the optional adapters. The core package autoloads and runs without it.

```php
$prophet = new Prophecy\Prophet();
$factory = new CoiSA\Factory\ProphesizeFactory(
    $prophet,
    Clock::class,
    static function (Prophecy\Prophecy\ObjectProphecy $clock): void {
        $clock->now()->willReturn('12:00')->shouldBeCalledOnce();
    },
);

$clock = $factory->create();
$clock->now();
$prophet->checkPredictions();
```

The test owns the Prophet. Verify it at the test's normal verification or teardown point. Returning a double, destroying a factory, or collecting garbage does not verify its predictions. The example and suite include a negative control: an unused `shouldBeCalled()` fails when the owner calls `checkPredictions()`. A framework-owned `ObjectProphecy` can instead be wrapped with `ProphecyFactory` while the framework retains verification ownership.

See [Prophecy's prediction documentation](https://github.com/phpspec/prophecy#predictions) and [PHP's final keyword contract](https://www.php.net/manual/en/language.oop5.final.php).

## Troubleshooting

| Symptom | Check |
| --- | --- |
| Your registration seems ignored | Confirm that the call uses the same resolver instance and exact identifier |
| A factory class needs constructor arguments | Register `new YourFactory($dependency)`, not its class name |
| A metadata factory throws | Repair that selected factory; resolution will not quietly use the product constructor |
| `Unknown named parameter` | Match the constructor/callback parameter names; use the resolver's argument array |
| A container lookup rejects arguments | PSR-11 does not accept them; use an explicit callback factory |
| A test unexpectedly uses production behavior | Find remaining direct constructor/static calls and make those seams explicit |
| A fake fails a concrete type check | Introduce a suitable interface at that consumer boundary |
| Prophecy classes cannot be found | Install the optional `phpspec/prophecy` test dependency |
| A prediction is never checked | Retain the Prophet and verify it at the end of the test |
| A circular dependency is reported | Follow the full identifier path, including aliases and callbacks |

Use a fresh resolver per test/request by default. You can remove or clear registrations explicitly. If multiple resolvers intentionally share a registry, clearing it affects every one of those resolvers. Factory does not implement automatic constructor injection, scope disposal, container building, object proxying or universal static/final/internal mocking.
