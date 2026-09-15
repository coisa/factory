<?php

declare(strict_types=1);

$root = realpath(dirname(__DIR__) . '/build/site');
if ($root === false) {
    throw new RuntimeException('Build the documentation first.');
}

/** @return DOMDocument */
function readHtml(string $file): DOMDocument
{
    $document = new DOMDocument();
    $previous = libxml_use_internal_errors(true);
    try {
        $document->loadHTML(file_get_contents($file));
    } finally {
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
    }
    return $document;
}

$pages = glob($root . '/*.html');
if (count($pages) !== 8) {
    throw new RuntimeException('Expected eight generated documentation pages.');
}
$links = 0;
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)) as $file) {
    if (strtolower($file->getExtension()) === 'php') {
        throw new RuntimeException('PHP must never be shipped to the static site.');
    }
}
foreach ($pages as $page) {
    $document = readHtml($page);
    $xpath = new DOMXPath($document);
    foreach (['//html[@lang="en"]', '//title', '//main[@id="main"]', '//nav[@aria-label]'] as $landmark) {
        if ($xpath->query($landmark)->length === 0) {
            throw new RuntimeException('Missing document landmark in ' . basename($page));
        }
    }
    foreach ($xpath->query('//*[@href or @src]') as $node) {
        $reference = $node->getAttribute($node->hasAttribute('href') ? 'href' : 'src');
        $parts = parse_url($reference);
        if ($parts === false) {
            throw new RuntimeException('Invalid link: ' . $reference);
        }
        if (isset($parts['scheme']) || isset($parts['host'])) {
            continue;
        }
        $path = rawurldecode($parts['path'] ?? '');
        $target = $path === '' ? $page : realpath(dirname($page) . '/' . $path);
        if ($target === false || !str_starts_with($target, $root . DIRECTORY_SEPARATOR) || !is_file($target)) {
            throw new RuntimeException('Broken local link in ' . basename($page) . ': ' . $reference);
        }
        if (isset($parts['fragment']) && $parts['fragment'] !== '') {
            $fragment = rawurldecode($parts['fragment']);
            $targetDocument = $target === $page ? $document : readHtml($target);
            $found = false;
            foreach ($targetDocument->getElementsByTagName('*') as $element) {
                if ($element->getAttribute('id') === $fragment) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                throw new RuntimeException('Broken fragment in ' . basename($page) . ': ' . $reference);
            }
        }
        ++$links;
    }
}
echo sprintf(
    "Verified %d static pages, %d local links/fragments, document landmarks and no PHP output.\n",
    count($pages),
    $links,
);
