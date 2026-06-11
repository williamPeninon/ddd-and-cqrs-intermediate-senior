<?php

declare(strict_types=1);

namespace FleetParking\Infra\Postgres;

use FleetParking\App\Query\FleetReadRepository;
use FleetParking\App\Query\ReadModel\FleetSummary;
use FleetParking\App\Query\ReadModel\FleetVehicleSummary;
use FleetParking\App\Query\ReadModel\VehicleLocationSummary;
use FleetParking\App\Query\ReadModel\VehicleSummary;
use PDO;

final readonly class PostgresFleetReadRepository implements FleetReadRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function findAllFleets(?string $ownerId = null): array
    {
        $sql = 'SELECT id, owner_id, created_at FROM fleets';
        $params = [];

        if ($ownerId !== null) {
            $sql .= ' WHERE owner_id = :owner_id';
            $params['owner_id'] = $ownerId;
        }

        $sql .= ' ORDER BY created_at ASC';

        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);

        return array_map(
            fn (array $row): FleetSummary => new FleetSummary(
                $row['id'],
                $row['owner_id'],
                self::formatTimestamp($row['created_at']),
            ),
            $statement->fetchAll(),
        );
    }

    public function findAllVehicles(): array
    {
        $statement = $this->pdo->query('SELECT plate_number FROM vehicles ORDER BY plate_number ASC');
        if ($statement === false) {
            return [];
        }

        return array_map(
            fn (array $row): VehicleSummary => new VehicleSummary($row['plate_number']),
            $statement->fetchAll(),
        );
    }

    public function findAllFleetVehicles(?string $fleetId = null): array
    {
        $sql = 'SELECT fleet_id, vehicle_plate_number FROM fleet_vehicles';
        $params = [];

        if ($fleetId !== null) {
            $sql .= ' WHERE fleet_id = :fleet_id';
            $params['fleet_id'] = $fleetId;
        }

        $sql .= ' ORDER BY fleet_id ASC, vehicle_plate_number ASC';

        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);

        return array_map(
            fn (array $row): FleetVehicleSummary => new FleetVehicleSummary(
                $row['fleet_id'],
                $row['vehicle_plate_number'],
            ),
            $statement->fetchAll(),
        );
    }

    public function findAllVehicleLocations(?string $fleetId = null): array
    {
        $sql = 'SELECT fleet_id, vehicle_plate_number, latitude, longitude, altitude, localized_at
                FROM vehicle_locations';
        $params = [];

        if ($fleetId !== null) {
            $sql .= ' WHERE fleet_id = :fleet_id';
            $params['fleet_id'] = $fleetId;
        }

        $sql .= ' ORDER BY localized_at DESC';

        $statement = $this->pdo->prepare($sql);
        $statement->execute($params);

        return array_map(
            fn (array $row): VehicleLocationSummary => new VehicleLocationSummary(
                $row['fleet_id'],
                $row['vehicle_plate_number'],
                (float) $row['latitude'],
                (float) $row['longitude'],
                $row['altitude'] === null ? null : (float) $row['altitude'],
                self::formatTimestamp($row['localized_at']),
            ),
            $statement->fetchAll(),
        );
    }

    private static function formatTimestamp(string $timestamp): string
    {
        return (new \DateTimeImmutable($timestamp))->format(\DateTimeInterface::ATOM);
    }
}
