<?php

declare(strict_types=1);

namespace CoiSA\Factory;

/**
 * An explicit construction strategy. Implementations may also return scalar values.
 *
 * @license MIT See LICENSE.
 */
interface FactoryInterface
{
    public function create(mixed ...$arguments): mixed;
}
