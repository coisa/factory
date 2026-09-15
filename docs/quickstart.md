# Installation & quickstart

Start with one construction call you want to control. Keep the consumer's dependency contract explicit.

## Version and installation

These pages describe the unreleased **2.0 development line**, not the currently published 1.x API. PHP 8.3 or newer and Composer 2 are required. Supported CI versions are 8.3, 8.4 and 8.5; later PHP versions are not yet verified.

In a checkout containing the 2.0 code:

```sh
composer install --no-plugins
composer --no-plugins check
php examples/quickstart.php
```

To evaluate that checkout inside another local application, use a Composer path repository. Run these commands from the application, adjusting the relative path to your Factory checkout:

```sh
composer config repositories.factory path ../factory
composer require 'coisa/factory:@dev' --no-plugins
```

This explicitly opts that one package into development code. Commit your application's Composer lock file. Remove the path repository and select a released version when you intentionally adopt a release. This change does not create a 2.0 release.

The runtime package requires only PHP and `psr/container`. The documentation generator, test runner and Prophecy are development dependencies of this checkout. Applications using the optional test-double adapters install Prophecy explicitly:

```sh
composer require --dev 'phpspec/prophecy:^1.22' --no-plugins
```

## Construct a class

```php
require 'vendor/autoload.php';

use CoiSA\Factory\Factory;

final readonly class Greeting
{
    public function __construct(public string $name)
    {
    }
}

$factory = new Factory();
$greeting = $factory->create(Greeting::class, ['name' => 'Ada']);
```

Pass a list for positional arguments or a map for named arguments. The array keeps constructor parameters such as `id` or `arguments` separate from the resolver's own parameters. Named argument names remain part of the chosen constructor's contract.

## Substitute at the seam

```php
use CoiSA\Factory\ValueFactory;

$factory->set(Greeting::class, new ValueFactory(new Greeting('test')));
$greeting = $factory->create(Greeting::class); // name is "test"

$factory->remove(Greeting::class);
$greeting = $factory->create(Greeting::class, ['name' => 'Ada']);
```

Only calls through this resolver see that registration. Another `new Factory()`, a direct `new Greeting(...)`, and a direct static call keep their normal PHP behavior.

For a complete, service-free example, run `php examples/quickstart.php`. The [examples](examples.md) show its exact source and checked output in the built site.
