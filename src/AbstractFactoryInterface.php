<?php

declare(strict_types=1);

namespace CoiSA\Factory;

/** Contract for a legacy class with an explicit static construction method. */
interface AbstractFactoryInterface
{
    public static function create(mixed ...$arguments): mixed;
}
