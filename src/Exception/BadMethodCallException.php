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

namespace CoiSA\Factory\Exception;

/**
 * Class BadMethodCallException
 *
 * @package CoiSA\Factory\Exception
 */
final class BadMethodCallException extends \BadMethodCallException implements FactoryException
{
    /**
     * @return BadMethodCallException
     */
    public static function forEmptyGivenArguments()
    {
        $message = 'You should inform at least one argument to create an instance.';

        return new self($message);
    }
}
