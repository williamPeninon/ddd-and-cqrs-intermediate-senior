<?php

declare(strict_types=1);

namespace FleetParking\Tests\Integration;

use FleetParking\Domain\Fleet\Fleet;
use FleetParking\Domain\Fleet\UserId;
use FleetParking\Domain\Location\Location;
use FleetParking\Domain\Vehicle\VehiclePlateNumber;
use FleetParking\Infra\Postgres\PdoFactory;
use FleetParking\Infra\Postgres\PostgresFleetReadRepository;
use FleetParking\Infra\Postgres\PostgresFleetRepository;
use FleetParking\Infra\Postgres\PostgresTestHelper;
use PHPUnit\Framework\TestCase;

final class PostgresFleetReadRepositoryTest extends TestCase
{
    private PostgresFleetRepository $writeRepository;
    private PostgresFleetReadRepository $readRepository;

    protected function setUp(): void
    {
        $pdo = PdoFactory::fromEnvironment();
        PostgresTestHelper::reset($pdo);
        $this->writeRepository = new PostgresFleetRepository($pdo);
        $this->readRepository = new PostgresFleetReadRepository($pdo);
    }

    public function testFindAllFleets(): void
    {
        $fleetA = Fleet::create(new UserId('user-1'));
        $fleetB = Fleet::create(new UserId('user-2'));
        $this->writeRepository->save($fleetA);
        $this->writeRepository->save($fleetB);

        $fleets = $this->readRepository->findAllFleets();

        self::assertCount(2, $fleets);
        self::assertSame('user-1', $fleets[0]->ownerId);
        self::assertSame('user-2', $fleets[1]->ownerId);
    }

    public function testFindAllFleetsFilteredByOwner(): void
    {
        $fleetA = Fleet::create(new UserId('user-1'));
        $fleetB = Fleet::create(new UserId('user-2'));
        $this->writeRepository->save($fleetA);
        $this->writeRepository->save($fleetB);

        $fleets = $this->readRepository->findAllFleets('user-1');

        self::assertCount(1, $fleets);
        self::assertSame((string) $fleetA->id(), $fleets[0]->id);
    }

    public function testFindAllVehiclesAndFleetVehicles(): void
    {
        $fleet = Fleet::create(new UserId('user-1'));
        $vehicle = new VehiclePlateNumber('AB-123-CD');
        $fleet->registerVehicle($vehicle);
        $this->writeRepository->save($fleet);

        self::assertCount(1, $this->readRepository->findAllVehicles());
        self::assertSame('AB-123-CD', $this->readRepository->findAllVehicles()[0]->plateNumber);

        $fleetVehicles = $this->readRepository->findAllFleetVehicles((string) $fleet->id());
        self::assertCount(1, $fleetVehicles);
        self::assertSame('AB-123-CD', $fleetVehicles[0]->plateNumber);
    }

    public function testFindAllVehicleLocations(): void
    {
        $fleet = Fleet::create(new UserId('user-1'));
        $vehicle = new VehiclePlateNumber('AB-123-CD');
        $location = new Location(48.8566, 2.3522, 35.0);
        $fleet->registerVehicle($vehicle);
        $fleet->parkVehicle($vehicle, $location);
        $this->writeRepository->save($fleet);

        $locations = $this->readRepository->findAllVehicleLocations((string) $fleet->id());

        self::assertCount(1, $locations);
        self::assertSame(48.8566, $locations[0]->latitude);
        self::assertSame(2.3522, $locations[0]->longitude);
        self::assertSame(35.0, $locations[0]->altitude);
    }
}
