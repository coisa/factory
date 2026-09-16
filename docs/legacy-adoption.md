# Adopt Factory in a legacy application

Start at one construction call. `LegacyFactory` is an opt-in static entrypoint for applications that cannot introduce constructor injection everywhere at once. It delegates to one stable `Factory` instance; ordinary `new Factory()` instances remain independent.

With the 2.0 package autoloaded, an existing PSR-11 `$container` and a service it can resolve, the bootstrap is two lines:

```php
CoiSA\Factory\LegacyFactory::setContainer($container);
$service = CoiSA\Factory\LegacyFactory::create(Service::class);
```

The bridge supports late installation: code can already call it before the container exists. No-argument classes use reflection, and registered strategies work normally. When bootstrap later installs a container, the next selection uses it. Registrations, aliases and previously injected references to `LegacyFactory::resolver()` survive that change.

Use this bridge for sequential legacy entrypoints or jobs. Use an application-owned resolver per request or concurrent task when state needs isolation. The bridge is shared process state, not a request or Fiber scope.

## Run the adoption examples

Follow the [2.0 installation guide](quickstart.md), then run from this source checkout:

```sh
php examples/legacy-bridge.php
php examples/legacy-to-di.php
```

The [bridge example](examples.md) exercises real PHP-DI autowiring, late bootstrap, aliases, overrides, captured factories and sequential jobs. The [static-to-DI example](examples.md) adapts a real static method call and verifies that an unchanged direct call still runs normally. Both use local fixtures and have exact output checks.

## Let your container autowire ordinary classes

Factory's core depends only on PSR-11. Autowiring belongs to the supplied container. PHP-DI is a development dependency here to test this integration; an application choosing PHP-DI installs it itself:

```sh
composer require 'php-di/php-di:^7.1.1' --no-plugins
```

This is the container setup used in the executable example, with ordinary constructor types and no attributes:

```php
use CoiSA\Factory\LegacyFactory;
use DI\ContainerBuilder;

final readonly class ReportSettings
{
    public function __construct(public string $prefix) {}
}

final readonly class Report
{
    public function __construct(
        private ReportSettings $settings,
        private string $title = 'daily',
    ) {}

    public function summary(): string
    {
        return $this->settings->prefix . ': ' . $this->title;
    }
}

$builder = new ContainerBuilder();
$builder->useAutowiring(true);
$builder->useAttributes(false);
$builder->addDefinitions([ReportSettings::class => new ReportSettings('local')]);

$container = $builder->build();
LegacyFactory::setContainer($container);
echo LegacyFactory::create(Report::class)->summary(); // local: daily
```

PHP-DI resolves `ReportSettings` into `Report`. Its normal `get()` behavior owns reuse; repeated resolution can return the same object. Scalars without defaults, interfaces and other ambiguous dependencies need container definitions. See PHP-DI's [autowiring](https://php-di.org/doc/autowiring.html) and [container configuration](https://php-di.org/doc/container-configuration.html) guides.

The installed container takes precedence over attributes, the static factory interface and reflection. A registry override takes precedence over the container. If a selected container service or factory throws, that error is visible; Factory does not silently try another strategy. PSR-11 error wrappers retain the original exception as described in the [API reference](api.md).

## Resolve a factory object, then register what it builds

A factory is also an ordinary object that a DI container can construct. Resolving its class returns that factory object; it does not automatically invoke the object's `create()` method a second time.

Continuing the preceding example:

```php
use CoiSA\Factory\CallableFactory;
use CoiSA\Factory\FactoryInterface;

final readonly class ReportFactory implements FactoryInterface
{
    public function __construct(private ReportSettings $settings) {}

    public function create(mixed ...$arguments): Report
    {
        return new Report($this->settings, ...$arguments);
    }
}

$resolver = LegacyFactory::resolver();
$resolver->set('reports', $resolver->create(ReportFactory::class));
$report = LegacyFactory::create('reports', ['title' => 'weekly']);
```

The container autowires `ReportFactory` without attributes; that configured factory is then reused to construct reports with per-call arguments. Its existing dependencies stay attached if the container is replaced later. PSR-11 itself accepts no construction arguments: passing `['title' => 'weekly']` directly to a container-selected `Report::class` lookup is rejected.

If the factory must follow later container changes, resolve it inside the callback on each call:

```php
$resolver->set('reports', new CallableFactory(
    static function (string $title) use ($resolver): Report {
        $builder = $resolver->create(ReportFactory::class);
        return $builder->create(title: $title);
    },
));
```

This chooses the current container on every call; it does not force that container to discard its cached factory. Keep the product identifier (`reports`) different from the builder identifier (`ReportFactory::class`) to avoid recursive lookup.

| Construction need | Registration |
| --- | --- |
| Class with no required constructor arguments | Call `create(HealthCheck::class)` without a container |
| Fresh no-argument factory on each lookup | `set('health', HealthCheckFactory::class)` where the class implements `FactoryInterface` |
| Factory with injected dependencies | `set('reports', $resolver->create(ReportFactory::class))` after container installation |
| Factory that follows the current container | Use the callback above |
| Existing static `create()` factory | Implement `AbstractFactoryInterface`, or register a `CallableFactory` around the selected legacy builder |

A string registration and an unregistered metadata factory class are instantiated without injected constructor arguments. Explicitly register a configured object or callback when dependencies are needed. The [strategy guide](strategies.md) explains these lifetimes in detail.

## Keep aliases and test overrides through bootstrap

Set up the seam before the container is ready:

```php
use CoiSA\Factory\ValueFactory;

$resolver = LegacyFactory::resolver();
$resolver->alias('public.report', Report::class);
$fakeReport = new Report(new ReportSettings('fake'), 'fixture');
$resolver->set(Report::class, new ValueFactory($fakeReport));

LegacyFactory::setContainer($container);
$report = LegacyFactory::create('public.report'); // the same $fakeReport
```

The alias and override remain registered because installing a container does not replace the resolver. Remove the override with `$resolver->remove(Report::class)` when the next call should reach the container. A fake must satisfy the consumer's actual type contract; registering it under a concrete class identifier does not make an unrelated object compatible with that class.

## Move one static caller toward injection

Suppose an existing method calls `LegacyMailApi::send($message)`. Put that operation behind an application interface:

```php
interface Sender
{
    public function send(string $message): string;
}

final class LegacySenderAdapter implements Sender
{
    public function send(string $message): string
    {
        return LegacyMailApi::send($message);
    }
}

LegacyFactory::resolver()->set(
    Sender::class,
    new CallableFactory(static fn (): Sender => new LegacySenderAdapter()),
);
```

Change the chosen caller to obtain a `Sender` through `LegacyFactory::create(Sender::class)` and invoke `$sender->send($message)`. Its public method can remain static during this step. A test can now register `new ValueFactory($fakeSender)` under `Sender::class`; no network or static-method interception is required.

Then migrate that caller's construction in small steps:

1. Inject `ResolverInterface` with `LegacyFactory::resolver()` while the consumer still selects dependencies. That reference sees later container changes and bridge resets.
2. Move the caller's creation into application bootstrap. Supply `new Factory($container)` to give that part of the application its own resolver and registry. Copy only the domain's intentional registrations into that resolver.
3. When the consumer needs only a `Sender`, change its constructor to accept `Sender` directly. Keep `FactoryInterface` only where repeated or deferred construction is part of the consumer's work.

The executable `legacy-to-di.php` shows the static caller, injected resolver and directly injected dependency side by side. Calls elsewhere to `LegacyMailApi::send()` still execute that method. A consumer that never reaches the chosen seam is unaffected; final/internal classes and PHP's type checks retain their normal restrictions.

## Own lifetime at job and test boundaries

`setContainer($next)` changes future selections; `setContainer(null)` detaches the container while preserving registrations. These operations neither reinject existing products nor rewrite strategy objects already returned by `getFactory()`.

| Reference already held | After a container change or bridge reset |
| --- | --- |
| `LegacyFactory::resolver()` | The same resolver sees the change; reset also clears its registry |
| An alias adapter | Resolves its target through that stable resolver when called; reset removes its registration but does not revoke an adapter already held |
| A selected `ContainerFactory` | Still calls the container captured during selection |
| A selected reflection adapter or configured factory object | Retains its own strategy and dependencies |
| An existing product | Keeps its existing state and dependencies |

Each in-flight container selection keeps the container it consulted, even if `has()` yields or changes configuration. This does not make the shared bridge safe for concurrent requests.

For sequential workers or tests, clear the bridge in `finally`, including on failure:

```php
LegacyFactory::reset();
try {
    LegacyFactory::setContainer($newContainerForThisJob);
    $job = LegacyFactory::create(Job::class);
    $job->run();
} finally {
    LegacyFactory::reset();
}
```

`reset()` clears registrations and aliases and detaches the container on the **same resolver**. Already injected resolver references observe the reset. It does not destroy returned objects, dispose resources or purge a container's services: PSR-11 provides no clear/reset operation. Reusing the same PHP-DI container also reuses its cached services. Build a new container for a new contextual job when those services carry job state, and let the application close its resources.

For concurrent requests, keep separate `Factory` instances and appropriately scoped containers; do not use `LegacyFactory` as their shared mutable bootstrap. Per-Fiber cycle bookkeeping in `Factory::create()` is independent of service and registry isolation.
