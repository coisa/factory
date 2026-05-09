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
 * Class ConstructorWithTypedArgument.
 *
 * @package CoiSA\Factory\Stub
 */
class ConstructorWithTypedArgument
{
    /** @var mixed[] */
    public $argument;

    /**
     * ConstructorWithTypedArgument constructor.
     *
     * @param mixed[] $argument
     */
    public function __construct(array $argument)
    {
        $this->argument = $argument;
    }

    /**
     * @return mixed[]
     */
    public function getArgument()
    {
        return $this->argument;
    }
}
