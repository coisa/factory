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
namespace CoiSA\Factory\Stub\Factory;

use CoiSA\Factory\FactoryInterface;

/**
 * Class TestFactory.
 *
 * @package CoiSA\Factory\Stub\Factory
 */
final class TestFactory implements FactoryInterface
{
    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return $this;
    }
}
