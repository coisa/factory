<?php

declare(strict_types=1);

/**
 * This file is part of coisa/factory.
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * @link      https://github.com/coisa/factory
 * @copyright Copyright (c) 2020-2022 Felipe Sayão Lobato Abreu <github@felipeabreu.com.br>
 * @license   https://opensource.org/licenses/MIT MIT License
 */

namespace examples\callable_factory;

use CoiSA\Factory\AbstractFactory;
use CoiSA\Factory\CallableFactory;

require_once \dirname(__DIR__) . '/vendor/autoload.php';

class RandomString
{
    public string $randomString;

    public function __construct(string $randomString)
    {
        $this->randomString = $randomString;
    }
}

$callable = fn (string $prefix = 'random_', string $suffix = '_string') => new RandomString(uniqid($prefix) . $suffix);
$factory = new CallableFactory($callable);

// Set the callable factory for the RandomString class
AbstractFactory::setFactory(RandomString::class, $factory);

return [
    __NAMESPACE__ => [
        'AbstractFactory::getFactory(RandomString::class)'          => AbstractFactory::getFactory(RandomString::class),
        'AbstractFactory::create(RandomString::class)'              => AbstractFactory::create(RandomString::class),
        'AbstractFactory::create(RandomString::class, \'prefix_\')' => AbstractFactory::create(
            RandomString::class,
            'prefix_'
        ),
        'AbstractFactory::create(RandomString::class, \'prefix_\', \'_suffix\')' => AbstractFactory::create(
            RandomString::class,
            'prefix_',
            '_suffix'
        ),
    ],
];
