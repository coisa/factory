<?php

declare(strict_types=1);

namespace CoiSA\Factory;

use Prophecy\Prophecy\ObjectProphecy;

/** Adapts an existing prophecy. Its owner must verify predictions. */
final readonly class ProphecyFactory implements FactoryInterface
{
    private ?\Closure $configure;

    /** @param ObjectProphecy<object> $objectProphecy */
    public function __construct(private ObjectProphecy $objectProphecy, ?callable $configure = null)
    {
        $this->configure = $configure === null ? null : \Closure::fromCallable($configure);
    }

    public function create(mixed ...$arguments): object
    {
        if ($this->configure !== null) {
            ($this->configure)($this->objectProphecy, $arguments);
        }

        return $this->objectProphecy->reveal();
    }
}
