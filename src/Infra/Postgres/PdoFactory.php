<?php

declare(strict_types=1);

namespace FleetParking\Infra\Postgres;

use PDO;

final class PdoFactory
{
    public static function fromEnvironment(): PDO
    {
        $dsn = getenv('DATABASE_DSN') ?: 'pgsql:host=127.0.0.1;port=5432;dbname=fleet';
        $user = getenv('DATABASE_USER') ?: 'fleet';
        $password = getenv('DATABASE_PASSWORD') ?: 'fleet';

        return new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
}
