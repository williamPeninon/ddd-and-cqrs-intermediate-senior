<?php

declare(strict_types=1);

namespace FleetParking\Infra\Postgres;

use PDO;

final class PostgresTestHelper
{
    public static function reset(PDO $pdo): void
    {
        $pdo->exec('TRUNCATE vehicle_locations, fleet_vehicles, vehicles, fleets CASCADE');
    }
}
