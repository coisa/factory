<?php

/**
 * This file is part of coisa/factory.
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * @link      https://github.com/coisa/factory
 *
 * @copyright Copyright (c) 2020 Felipe Sayão Lobato Abreu <github@felipeabreu.com.br>
 * @license   https://opensource.org/licenses/MIT MIT License
 */
namespace CoiSA\Factory;

/**
 * Class ValueFactory.
 *
 * @package CoiSA\Factory
 */
final class ValueFactory implements FactoryInterface
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * ValueFactory constructor.
     *
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return $this->value;
    }
}
