<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use CoiSA\Factory\Attribute\FactoryAttribute;
use CoiSA\Factory\Factory;
use CoiSA\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

#[FactoryAttribute('message.builder')]
final readonly class Message
{
    public function __construct(public string $text)
    {
    }
}

final readonly class MessageFactory implements FactoryInterface
{
    public function __construct(private string $prefix)
    {
    }

    public function create(mixed ...$arguments): Message
    {
        return new Message($this->prefix . $arguments['text']);
    }
}

$factory = new Factory();
$factory->set('message.builder', new MessageFactory('ready: '));
$factory->alias('message', Message::class);
echo $factory->create('message', ['text' => 'hello'])->text, PHP_EOL;

// A PSR-11 lookup delegates lifetime and value ownership to the supplied container.
$container = new class implements ContainerInterface {
    public function has(string $id): bool
    {
        return $id === 'timezone';
    }

    public function get(string $id): mixed
    {
        if (!$this->has($id)) {
            throw new class ('Unknown entry') extends RuntimeException implements
                Psr\Container\NotFoundExceptionInterface
            {
            };
        }
        return 'UTC';
    }
};
echo (new Factory($container))->create('timezone'), PHP_EOL;
