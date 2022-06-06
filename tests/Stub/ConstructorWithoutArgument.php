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

namespace CoiSA\Factory\Stub;

/**
 * Class ConstructorWithoutArgument.
 *
 * @package CoiSA\Factory\Stub
 */
class ConstructorWithoutArgument
{
    /**
     * @var string
     */
    public $argument;

    /**
     * ConstructorWithoutArgument constructor.
     */
    public function __construct()
    {
        $this->argument = uniqid('test', true);
    }
}
