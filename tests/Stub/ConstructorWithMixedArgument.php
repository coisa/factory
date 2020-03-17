<?php

namespace CoiSA\Factory\Stub;

/**
 * Class ConstructorWithMixedArgument
 *
 * @package CoiSA\Factory\Stub
 */
final class ConstructorWithMixedArgument
{
    /** @var array */
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
