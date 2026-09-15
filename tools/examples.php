<?php

declare(strict_types=1);

/** @return array<string, string> */
function verifiedExamples(): array
{
    $root = dirname(__DIR__);
    $expected = json_decode(file_get_contents($root . '/examples/expected.json'), true, flags: JSON_THROW_ON_ERROR);
    $actualFiles = array_map('basename', glob($root . '/examples/*.php'));
    $expectedFiles = array_keys($expected);
    sort($actualFiles);
    sort($expectedFiles);
    if ($actualFiles !== $expectedFiles) {
        throw new RuntimeException('Every executable example must have an expected output fixture.');
    }

    foreach ($expected as $file => $output) {
        $process = proc_open(
            [PHP_BINARY, '-d', 'display_errors=stderr', '-d', 'error_reporting=-1', $root . '/examples/' . $file],
            [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            $root,
        );
        if (!is_resource($process)) {
            throw new RuntimeException('Cannot run example ' . $file);
        }
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        $status = proc_close($process);
        if ($status !== 0 || $stderr !== '' || $stdout !== $output) {
            throw new RuntimeException(
                sprintf("Example %s failed (exit %d).\nSTDOUT:\n%s\nSTDERR:\n%s", $file, $status, $stdout, $stderr),
            );
        }
    }

    return $expected;
}
