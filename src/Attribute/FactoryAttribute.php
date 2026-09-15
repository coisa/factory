<?php

declare(strict_types=1);

namespace CoiSA\Factory\Attribute;

/** Names a registered strategy or a no-argument factory class. */
#[\Attribute(\Attribute::TARGET_CLASS)]
final readonly class FactoryAttribute
{
    public function __construct(public string $factory)
    {
    }
}
