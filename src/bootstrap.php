<?php

declare(strict_types=1);

spl_autoload_register(function (string $class): void {
    $prefix = 'DrivebyLab\\';
    if (!str_starts_with($class, $prefix)) {
        return;
    }

    $relative = str_replace('\\', DIRECTORY_SEPARATOR, substr($class, strlen($prefix)));
    $file = __DIR__ . DIRECTORY_SEPARATOR . $relative . '.php';

    if (is_file($file)) {
        require $file;
    }
});

\DrivebyLab\Support\Env::load(__DIR__ . '/../.env');
date_default_timezone_set((string) \DrivebyLab\Support\Env::get('APP_TIMEZONE', 'UTC'));
