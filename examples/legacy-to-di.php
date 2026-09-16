<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use CoiSA\Factory\CallableFactory;
use CoiSA\Factory\LegacyFactory;
use CoiSA\Factory\ResolverInterface;
use CoiSA\Factory\ValueFactory;

interface Sender
{
    public function send(string $message): string;
}

// A local stand-in for a legacy API. This example never contacts a service.
final class LegacyMailApi
{
    public static function send(string $message): string
    {
        return "local: {$message}";
    }
}

// Step 1: put the hardcoded static collaborator behind the application's interface.
final class LegacySenderAdapter implements Sender
{
    public function send(string $message): string
    {
        return LegacyMailApi::send($message);
    }
}

// A legacy caller can keep its public static entrypoint while gaining a seam.
final class LegacyReport
{
    public static function deliver(): string
    {
        $sender = LegacyFactory::create(Sender::class);
        if (!$sender instanceof Sender) {
            throw new UnexpectedValueException('The factory must create a Sender.');
        }
        return $sender->send('report');
    }
}

// Step 2: remove global lookup from a migrated consumer by injecting the resolver.
final readonly class ReportWithResolver
{
    public function __construct(private ResolverInterface $factory)
    {
    }

    public function deliver(): string
    {
        $sender = $this->factory->create(Sender::class);
        if (!$sender instanceof Sender) {
            throw new UnexpectedValueException('The factory must create a Sender.');
        }
        return $sender->send('report');
    }
}

// Step 3: once construction is in the composition root, inject the dependency itself.
final readonly class Report
{
    public function __construct(private Sender $sender)
    {
    }

    public function deliver(): string
    {
        return $this->sender->send('report');
    }
}

LegacyFactory::reset();
try {
    $resolver = LegacyFactory::resolver();
    $resolver->set(Sender::class, new CallableFactory(static fn (): Sender => new LegacySenderAdapter()));
    echo 'Adapted: ', LegacyReport::deliver(), PHP_EOL;

    $fake = new class implements Sender {
        public function send(string $message): string
        {
            return "fake: {$message}";
        }
    };
    $resolver->set(Sender::class, new ValueFactory($fake));
    echo 'Test: ', LegacyReport::deliver(), PHP_EOL;
    echo 'Injected resolver: ', (new ReportWithResolver($resolver))->deliver(), PHP_EOL;
    echo 'Direct DI: ', (new Report($resolver->create(Sender::class)))->deliver(), PHP_EOL;
    echo 'Unchanged direct call: ', LegacyMailApi::send('report'), PHP_EOL;
} finally {
    LegacyFactory::reset();
}
