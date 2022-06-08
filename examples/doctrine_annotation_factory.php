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

namespace examples\doctrine_annotation_factory;

use CoiSA\Factory\AbstractFactory;
use CoiSA\Factory\AbstractFactoryInterface;
use CoiSA\Factory\FactoryInterface;

require_once \dirname(__DIR__) . '/vendor/autoload.php';

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
    public function create()
    {
        return new RandomString(uniqid('random_'));
    }
}

/**
 * Define the RandomInt class and register the abstract factory through the annotation.
 *
 * @CoiSA\Factory\Annotation\Factory(factory="examples\doctrine_annotation_factory\RandomIntAbstractFactory")
 */
class RandomInt
{
    public int $randomInt;

    public function __construct(int $randomInt)
    {
        $this->randomInt = $randomInt;
    }
}

/**
 * Define the RandomString class and register the factory through the annotation.
 *
 * @CoiSA\Factory\Annotation\Factory(factory="examples\doctrine_annotation_factory\RandomStringFactory")
 */
class RandomString
{
    public string $randomString;

    public function __construct(string $randomString)
    {
        $this->randomString = $randomString;
    }
}

return [
    __NAMESPACE__ => [
        'AbstractFactory::getFactory(RandomInt::class)'    => AbstractFactory::getFactory(RandomInt::class),
        'AbstractFactory::create(RandomInt::class)'        => AbstractFactory::create(RandomInt::class),
        'AbstractFactory::getFactory(RandomString::class)' => AbstractFactory::getFactory(RandomString::class),
        'AbstractFactory::create(RandomString::class)'     => AbstractFactory::create(RandomString::class),
    ],
];
