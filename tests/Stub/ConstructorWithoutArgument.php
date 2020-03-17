<?php

namespace CoiSA\Factory\Stub;

/**
 * Class ConstructorWithoutArgument
 *
 * @package CoiSA\Factory\Stub
 */
final class ConstructorWithoutArgument
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
        $this->argument = \uniqid('test', true);
    }
}
