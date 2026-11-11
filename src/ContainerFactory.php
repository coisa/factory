<?php

declare(strict_types=1);

namespace CoiSA\Factory;

use CoiSA\Factory\Exception\ContainerException;
use CoiSA\Factory\Exception\ContainerNotFoundException;
use CoiSA\Factory\Exception\InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/** PSR-11 owns the returned value and its lifecycle. */
final readonly class ContainerFactory implements FactoryInterface
{
    public function __construct(private ContainerInterface $container, private string $id)
    {
    }

    public function create(mixed ...$arguments): mixed
    {
        if ($arguments !== []) {
            throw new InvalidArgumentException('PSR-11 lookups do not accept construction arguments.');
        }

        try {
            return $this->container->get($this->id);
        } catch (NotFoundExceptionInterface $exception) {
            throw new ContainerNotFoundException(
                sprintf('Container entry "%s" was not found.', $this->id),
                0,
                $exception,
            );
        } catch (ContainerExceptionInterface $exception) {
            throw new ContainerException(
                sprintf('Container could not resolve "%s".', $this->id),
                0,
                $exception,
            );
        }
    }
}
