<?php

declare(strict_types=1);

namespace DrivebyLab\Support;

final class Json
{
    public static function encode(array $payload): string
    {
        return json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    }
}
