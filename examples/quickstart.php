<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use CoiSA\Factory\Factory;
use CoiSA\Factory\ValueFactory;

final readonly class Greeting
{
    public function __construct(public string $name)
    {
    }

    public function text(): string
    {
        return "Hello, {$this->name}!";
    }
}

$factory = new Factory();
$greeting = $factory->create(Greeting::class, ['name' => 'Ada']);
echo $greeting->text(), PHP_EOL;

$factory->set(Greeting::class, new ValueFactory(new Greeting('test')));
echo $factory->create(Greeting::class)->text(), PHP_EOL;
