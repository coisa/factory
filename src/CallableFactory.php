<?php

declare(strict_types=1);

namespace CoiSA\Factory;

final readonly class CallableFactory implements FactoryInterface
{
    private \Closure $callback;

    public function __construct(callable $callback)
    {
        $this->callback = \Closure::fromCallable($callback);
    }

    public function create(mixed ...$arguments): mixed
    {
        return ($this->callback)(...$arguments);
    }
}
