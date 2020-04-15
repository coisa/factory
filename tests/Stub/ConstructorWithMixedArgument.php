<?php

/**
 * This file is part of coisa/factory.
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * @link      https://github.com/coisa/factory
 * @copyright Copyright (c) 2020 Felipe Sayão Lobato Abreu <github@felipeabreu.com.br>
 * @license   https://opensource.org/licenses/MIT MIT License
 */

namespace CoiSA\Factory\Stub;

/**
 * Class ConstructorWithMixedArgument
 *
 * @package CoiSA\Factory\Stub
 */
final class ConstructorWithMixedArgument
{
    /** @var mixed */
    public $argument;

    /**
     * ConstructorWithMixedArgument constructor.
     *
     * @param mixed $argument
     */
    public function __construct($argument)
    {
        $this->argument = $argument;
    }
}
