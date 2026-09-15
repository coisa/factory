<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use CoiSA\Factory\CallableFactory;
use CoiSA\Factory\FactoryInterface;
use CoiSA\Factory\ValueFactory;

interface Sender
{
    public function send(string $message): string;
}

// A local stand-in for a legacy API. This example never contacts a service.
final class LegacySender implements Sender
{
    public static function connect(): self
    {
        return new self();
    }

    public function send(string $message): string
    {
        return "local: {$message}";
    }
}

// Step 1: move the construction call to an injected, explicit seam.
final readonly class ReportWithFactory
{
    public function __construct(private FactoryInterface $senderFactory)
    {
    }

    public function deliver(): string
    {
        $sender = $this->senderFactory->create();
        if (!$sender instanceof Sender) {
            throw new UnexpectedValueException('The factory must create a Sender.');
        }
        return $sender->send('report');
    }
}

$live = new ReportWithFactory(new CallableFactory([LegacySender::class, 'connect']));
echo 'Adapted: ', $live->deliver(), PHP_EOL;

// Step 2: replace that seam in a test with an interface-compatible fake.
$fake = new class implements Sender {
    public function send(string $message): string
    {
        return "fake: {$message}";
    }
};
$test = new ReportWithFactory(new ValueFactory($fake));
echo 'Test: ', $test->deliver(), PHP_EOL;

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

echo 'Direct DI: ', (new Report($fake))->deliver(), PHP_EOL;
echo 'Unchanged direct call: ', LegacySender::connect()->send('report'), PHP_EOL;
