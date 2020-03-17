<?php

namespace CoiSA\Factory\Stub;

/**
 * Class ConstructorWithTypedArgument
 *
 * @package CoiSA\Factory\Stub
 */
final class ConstructorWithTypedArgument
{
    /** @var array */
    public $argument;

    /**
     * ConstructorWithTypedArgument constructor.
     *
     * @param array $argument
     */
    public function __construct(array $argument)
    {
        $this->argument = $argument;
    }
}
