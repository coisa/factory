<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use CoiSA\Factory\ProphesizeFactory;
use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\Prophet;

interface Clock
{
    public function now(): string;
}

// The test owns the Prophet and its verification lifecycle.
$prophet = new Prophet();
$factory = new ProphesizeFactory($prophet, Clock::class, static function (ObjectProphecy $clock): void {
    $clock->now()->willReturn('12:00')->shouldBeCalledOnce();
});

echo $factory->create()->now(), PHP_EOL;
$prophet->checkPredictions(); // Removing the now() call above makes this fail.
echo 'Predictions checked.', PHP_EOL;
