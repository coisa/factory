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

use CoiSA\Factory\AbstractFactory;
use CoiSA\Factory\AbstractFactoryInterface;

require_once dirname(__DIR__) . '/vendor/autoload.php';

/*
 * You can run this example with the following command:
 * $ docker run --rm -v $(pwd):/app -w /app php:alpine php examples/callable_factory.php
 */

class RandomString
{
    public string $randomString;

    public function __construct(string $prefix = 'random_', string $suffix = '_string')
    {
        $this->randomString = uniqid($prefix) . $suffix;
    }
}

class RandomStringAbtractFactory implements AbstractFactoryInterface
{
    public static function create()
    {
        return new RandomString(...func_get_args());
    }
}

// Set the callable factory for the RandomString class
// @TODO setAbstractFactory?? Can we set a factory and an abstract factory for the same object?
AbstractFactory::setFactory(RandomString::class, RandomStringAbtractFactory::class);

var_dump(
    AbstractFactory::create(RandomString::class),
    AbstractFactory::create(RandomString::class, 'prefix_'),
    AbstractFactory::create(RandomString::class, 'prefix_', '_suffix')
);
