<?php

declare(strict_types=1);

namespace DrivebyLab\Security;

use DrivebyLab\Support\View;

final class Csrf
{
    private const SESSION_KEY = '_csrf_token';

    public static function start(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_set_cookie_params([
                'httponly' => true,
                'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
                'samesite' => 'Strict',
            ]);
            session_start();
        }

        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }
    }

    public static function field(): string
    {
        $token = View::e((string) ($_SESSION[self::SESSION_KEY] ?? ''));
        return '<input type="hidden" name="_csrf_token" value="' . $token . '">';
    }

    public static function valid(?string $token): bool
    {
        return is_string($token)
            && isset($_SESSION[self::SESSION_KEY])
            && hash_equals((string) $_SESSION[self::SESSION_KEY], $token);
    }
}
