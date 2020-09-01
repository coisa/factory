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
 * Class InvalidArgumentException
 *
 * @package CoiSA\Factory\Exception
 */
final class InvalidArgumentException extends \InvalidArgumentException implements FactoryException
{
    /**
     * @param mixed $argument
     *
     * @return InvalidArgumentException
     */
    public static function isNotCallable($argument)
    {
        $message = \sprintf(
            'Given argument "%s" are not a valid callable function.',
            $argument
        );

        return new self($message);
    }

    /**
     * @param string $class
     *
     * @return InvalidArgumentException
     */
    public static function forClassNotFound($class)
    {
        $message = \sprintf(
            'Class "%s" not found!',
            $class
        );

        return new self($message);
    }
}
