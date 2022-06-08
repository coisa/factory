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
use CoiSA\Factory\FactoryInterface;

require_once dirname(__DIR__) . '/vendor/autoload.php';

/*
 * You can run this example with the following command:
 * $ docker run --rm -v $(pwd):/app -w /app php:alpine php examples/doctrine_annotation_factory.php
 */

/**
 * Define an abstract factory for the RandomInt class.
 */
class RandomIntAbstractFactory implements AbstractFactoryInterface
{
    public static function create()
    {
        return new RandomInt(random_int(1, 100));
    }
}

/**
 * Define a factory for the RandomString class.
 */
class RandomStringFactory implements FactoryInterface
{
    private string $prefix;

    public function __construct(string $prefix = 'random')
    {
        $this->prefix = $prefix;
    }

    public function create()
    {
        return new RandomString(uniqid($this->prefix));
    }
}

/**
 * Define the RandomInt class and register the abstract factory through the attribute.
 */
#[CoiSA\Factory\Attribute\Factory(factory: RandomIntAbstractFactory::class)]
class RandomInt
{
    public int $randomInt;

    public function __construct(int $randomInt)
    {
        $this->randomInt = $randomInt;
    }
}

/**
 * Define the RandomString class and register the factory through the attribute.
 */
#[CoiSA\Factory\Attribute\Factory(factory: RandomStringFactory::class)]
class RandomString
{
    public string $randomString;

    public function __construct(string $randomString)
    {
        $this->randomString = $randomString;
    }
}
#[CoiSA\Factory\Attribute\Factory(factory: new RandomStringFactory('other'))]
class RandomOtherString
{
    public string $randomString;

    public function __construct(string $randomString)
    {
        $this->randomString = $randomString;
    }
}

// Create the instances using the factories given on annotations.
var_dump(
    AbstractFactory::create(RandomInt::class),
    AbstractFactory::create(RandomString::class),
    AbstractFactory::create(RandomOtherString::class)
);

// Return the factory instances used for construction of the objects.
var_dump(
    AbstractFactory::getFactory(RandomInt::class),
    AbstractFactory::getFactory(RandomString::class),
    AbstractFactory::getFactory(RandomOtherString::class)
);
