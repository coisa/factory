<?php

declare(strict_types=1);

namespace CoiSA\Factory;

use Prophecy\Prophet;

/** Creates a fresh double per call using a caller-owned Prophet. */
final readonly class ProphesizeFactory implements FactoryInterface
{
    private ?\Closure $configure;

    /** @param class-string $classOrInterface */
    public function __construct(
        private Prophet $prophet,
        private string $classOrInterface,
        ?callable $configure = null,
    ) {
        $this->configure = $configure === null ? null : \Closure::fromCallable($configure);
    }

    public function create(mixed ...$arguments): object
    {
        $prophecy = $this->prophet->prophesize($this->classOrInterface);

        if ($this->configure !== null) {
            ($this->configure)($prophecy, $arguments);
        }

        return $prophecy->reveal();
    }
}
