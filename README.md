# CoiSA Factory

**Make object construction an explicit, replaceable boundary.**

Factory is a small PHP library for moving legacy construction code toward dependency injection. Use reflection, callbacks, registered values, aliases, PHP attributes, PSR-11 containers, or optional Prophecy adapters through one deliberate seam.

This checkout is the **unreleased 2.0 development line**, requiring PHP 8.3+. CI covers PHP 8.3, 8.4 and 8.5. The published 1.x API is different; do not apply these examples to 1.x.

```php
use CoiSA\Factory\Factory;
use CoiSA\Factory\ValueFactory;

$factory = new Factory();
$greeting = $factory->create(Greeting::class, ['name' => 'Ada']);

$factory->set(Greeting::class, new ValueFactory(new Greeting('test')));
$testGreeting = $factory->create(Greeting::class);
```

The complete [quickstart](examples/quickstart.php) runs without a framework or external service. Each resolver owns its registry by default. It does not intercept arbitrary `new` expressions or direct static method calls.

## Try this checkout

Install PHP 8.3+ and Composer 2. In a checkout containing this development line:

```sh
composer install --no-plugins
composer --no-plugins check
php examples/legacy-to-di.php
```

For application installation from a local checkout, follow the [installation guide](docs/quickstart.md). No 2.0 tag is published by this change.

## Learn the boundary

- [Installation and quickstart](docs/quickstart.md)
- [Construction strategies and lifetimes](docs/strategies.md)
- [API and error reference](docs/api.md)
- [Executable examples: legacy → seam → DI](docs/examples.md)
- [Testing, limits and troubleshooting](docs/testing.md)
- [Migration from 1.x](docs/migration.md)
- [Contributing, compatibility and documentation builds](CONTRIBUTING.md)
- [Agent maintenance guide](AGENTS.md)
- [Factory construction skill](.agents/skills/factory-construction/SKILL.md)
- [Changelog](CHANGELOG.md)

`composer --no-plugins docs` builds the static reference site in `build/site/`, executes its PHP examples and validates local links. PHP runs during the build, never in GitHub Pages. Deployment is a separate manual workflow restricted to the default branch.

## Credits and license

Created by [Felipe Sayão Lobato Abreu](https://github.com/coisa).
Released under the [MIT license](LICENSE).
