<?php

declare(strict_types=1);

namespace FleetParking\Infra\Postgres;

use FleetParking\Domain\Exception\FleetNotFound;
use FleetParking\Domain\Fleet\Fleet;
use FleetParking\Domain\Fleet\FleetId;
use FleetParking\Domain\Fleet\FleetRepository;
use FleetParking\Domain\Fleet\UserId;
use PDO;

final readonly class PostgresFleetRepository implements FleetRepository
{
    public function __construct(private PDO $pdo)
    {
    }

    public function save(Fleet $fleet): void
    {
        $this->pdo->beginTransaction();
        try {
            $this->pdo->prepare(
                'INSERT INTO fleets (id, owner_id) VALUES (:id, :owner_id)
                 ON CONFLICT (id) DO UPDATE SET owner_id = EXCLUDED.owner_id'
            )->execute([
                'id' => (string) $fleet->id(),
                'owner_id' => (string) $fleet->ownerId(),
            ]);

            $this->pdo->prepare('DELETE FROM vehicle_locations WHERE fleet_id = :fleet_id')->execute([
                'fleet_id' => (string) $fleet->id(),
            ]);
            $this->pdo->prepare('DELETE FROM fleet_vehicles WHERE fleet_id = :fleet_id')->execute([
                'fleet_id' => (string) $fleet->id(),
            ]);

            foreach ($fleet->vehicles() as $vehicle) {
                $plate = (string) $vehicle;
                $this->pdo->prepare('INSERT INTO vehicles (plate_number) VALUES (:plate) ON CONFLICT DO NOTHING')
                    ->execute(['plate' => $plate]);
                $this->pdo->prepare('INSERT INTO fleet_vehicles (fleet_id, vehicle_plate_number) VALUES (:fleet_id, :plate)')
                    ->execute(['fleet_id' => (string) $fleet->id(), 'plate' => $plate]);
            }

            foreach ($fleet->locations() as $plate => $location) {
                $this->pdo->prepare(
                    'INSERT INTO vehicle_locations (fleet_id, vehicle_plate_number, latitude, longitude, altitude)
                     VALUES (:fleet_id, :plate, :lat, :lng, :alt)'
                )->execute([
                    'fleet_id' => (string) $fleet->id(),
                    'plate' => $plate,
                    'lat' => $location->latitude,
                    'lng' => $location->longitude,
                    'alt' => $location->altitude,
                ]);
            }

            $this->pdo->commit();
        } catch (\Throwable $exception) {
            $this->pdo->rollBack();
            throw $exception;
        }
    }

    public function get(FleetId $id): Fleet
    {
        $statement = $this->pdo->prepare('SELECT id, owner_id FROM fleets WHERE id = :id');
        $statement->execute(['id' => (string) $id]);
        $fleetRow = $statement->fetch();

        if (!$fleetRow) {
            throw FleetNotFound::withId((string) $id);
        }

        $vehiclesStatement = $this->pdo->prepare('SELECT vehicle_plate_number FROM fleet_vehicles WHERE fleet_id = :id');
        $vehiclesStatement->execute(['id' => (string) $id]);
        $vehicles = array_map(
            static fn (array $row): string => $row['vehicle_plate_number'],
            $vehiclesStatement->fetchAll(),
        );

        $locationsStatement = $this->pdo->prepare(
            'SELECT vehicle_plate_number, latitude, longitude, altitude FROM vehicle_locations WHERE fleet_id = :id'
        );
        $locationsStatement->execute(['id' => (string) $id]);

        $locations = [];
        foreach ($locationsStatement->fetchAll() as $row) {
            $locations[$row['vehicle_plate_number']] = [
                'lat' => (float) $row['latitude'],
                'lng' => (float) $row['longitude'],
                'alt' => $row['altitude'] === null ? null : (float) $row['altitude'],
            ];
        }

        return Fleet::reconstitute(
            new FleetId($fleetRow['id']),
            new UserId($fleetRow['owner_id']),
            $vehicles,
            $locations,
        );
    }
}
