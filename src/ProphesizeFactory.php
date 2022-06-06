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

namespace CoiSA\Factory;

use CoiSA\Factory\Exception\InvalidArgumentException;
use Prophecy\Prophet;

/**
 * Class ProphesizeFactory.
 *
 * @package CoiSA\Factory
 */
final class ProphesizeFactory implements FactoryInterface
{
    /**
     * @var string
     */
    private $classOrInterface;

    /**
     * @var null|callable
     */
    private $prophesizeMethodsCallable;

    /**
     * ProphesizeFactory constructor.
     *
     * @param string        $classOrInterface
     * @param null|callable $prophesizeMethodsCallable
     */
    public function __construct($classOrInterface, $prophesizeMethodsCallable = null)
    {
        if ($prophesizeMethodsCallable && false === \is_callable($prophesizeMethodsCallable)) {
            throw InvalidArgumentException::forInvalidArgumentType(
                'prophesizeMethodsCallable',
                'callable'
            );
        }

        $this->classOrInterface          = $classOrInterface;
        $this->prophesizeMethodsCallable = $prophesizeMethodsCallable;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $prophet        = new Prophet();
        $objectProphecy = $prophet->prophesize($this->classOrInterface);

        if ($this->prophesizeMethodsCallable) {
            \call_user_func($this->prophesizeMethodsCallable, $objectProphecy, \func_get_args());
        }

        return $objectProphecy->reveal();
    }
}
