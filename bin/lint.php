<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$paths = [
    $root . '/public/index.php',
    $root . '/src',
    $root . '/bin',
];

$files = [];
foreach ($paths as $path) {
    if (is_file($path)) {
        $files[] = $path;
        continue;
    }

    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $files[] = $file->getPathname();
        }
    }
}

sort($files);
$failed = false;
foreach ($files as $file) {
    passthru(escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($file), $code);
    if ($code !== 0) {
        $failed = true;
    }
}

exit($failed ? 1 : 0);
