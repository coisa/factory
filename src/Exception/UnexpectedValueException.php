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
 * Class UnexpectedValueException
 *
 * @package CoiSA\Factory\Exception
 */
final class UnexpectedValueException extends \UnexpectedValueException implements FactoryException
{
    public static function expectClassImplements($class, $implements)
    {
        $message = \sprintf(
            'Expected class "%s" to implement "%s".',
            $class,
            $implements
        );

        return new self($message);
    }
}
