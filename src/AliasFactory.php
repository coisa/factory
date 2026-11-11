<?php

declare(strict_types=1);

namespace CoiSA\Factory;

/** Resolve the target at call time so subsequent registrations remain visible. */
final readonly class AliasFactory implements FactoryInterface
{
    public function __construct(private ResolverInterface $resolver, private string $target)
    {
    }

    public function create(mixed ...$arguments): mixed
    {
        return $this->resolver->create($this->target, $arguments);
    }
}
