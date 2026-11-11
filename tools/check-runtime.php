<?php

declare(strict_types=1);

require $argv[1] ?? dirname(__DIR__) . '/vendor/autoload.php';

use CoiSA\Factory\CallableFactory;
use CoiSA\Factory\Factory;
use CoiSA\Factory\LegacyFactory;
use CoiSA\Factory\ReflectionClassFactory;
use CoiSA\Factory\ValueFactory;

if (
    class_exists(Prophecy\Prophet::class)
    || class_exists(DI\Container::class)
    || class_exists(Doctrine\Common\Annotations\AnnotationReader::class)
) {
    throw new RuntimeException('Run the core smoke test with production-only dependencies.');
}
if (class_exists('CoiSA\\Factory\\AbstractFactory') || class_exists('CoiSA\\Factory\\Attribute\\Factory')) {
    throw new RuntimeException('Retired global facades or Composer aliases must not be autoloaded.');
}

$factory = new Factory();
$factory->set('value', new ValueFactory('core'));
$factory->alias('alias', 'value');
$factory->set('callback', new CallableFactory(static fn (string $name): string => 'Hello ' . $name));
if ($factory->create('alias') !== 'core' || $factory->create('callback', ['name' => 'Ada']) !== 'Hello Ada') {
    throw new RuntimeException('Production-only strategy smoke test failed.');
}
$date = (new ReflectionClassFactory(DateTimeImmutable::class))->create(datetime: '2026-01-02');
if ($date->format('Y-m-d') !== '2026-01-02') {
    throw new RuntimeException('Production-only reflection smoke test failed.');
}
$legacy = LegacyFactory::resolver();
try {
    $legacy->set('value', new ValueFactory('bridge'));
    if (LegacyFactory::create('value') !== 'bridge' || $factory->create('alias') !== 'core') {
        throw new RuntimeException('The opt-in bridge must remain independent of an ordinary resolver.');
    }
} finally {
    LegacyFactory::reset();
}
if ($legacy !== LegacyFactory::resolver() || $legacy->has('value')) {
    throw new RuntimeException('Bridge reset must clear its stable resolver.');
}
echo 'Core autoload and strategies work without development or optional adapter dependencies.', PHP_EOL;
