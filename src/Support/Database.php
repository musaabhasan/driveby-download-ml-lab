<?php

declare(strict_types=1);

namespace DrivebyLab\Support;

use PDO;
use PDOException;

final class Database
{
    public static function tryConnection(): ?PDO
    {
        $host = Env::get('DB_HOST', 'mysql');
        $port = Env::get('DB_PORT', '3306');
        $database = Env::get('DB_DATABASE', 'driveby_lab');
        $username = Env::get('DB_USERNAME', 'driveby_lab');
        $password = Env::get('DB_PASSWORD', '');
        $dsn = "mysql:host={$host};port={$port};dbname={$database};charset=utf8mb4";

        try {
            return new PDO($dsn, (string) $username, (string) $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException) {
            return null;
        }
    }
}
