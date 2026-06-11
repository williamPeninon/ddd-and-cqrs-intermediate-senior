<?php

declare(strict_types=1);

namespace FleetParking\Tests\Integration;

use FleetParking\Domain\Fleet\Fleet;
use FleetParking\Domain\Fleet\FleetId;
use FleetParking\Domain\Fleet\UserId;
use FleetParking\Domain\Location\Location;
use FleetParking\Domain\Vehicle\VehiclePlateNumber;
use FleetParking\Infra\Postgres\PdoFactory;
use FleetParking\Infra\Postgres\PostgresFleetRepository;
use FleetParking\Infra\Postgres\PostgresTestHelper;
use PHPUnit\Framework\TestCase;

final class PostgresFleetRepositoryTest extends TestCase
{
    private PostgresFleetRepository $repository;

    protected function setUp(): void
    {
        $pdo = PdoFactory::fromEnvironment();
        PostgresTestHelper::reset($pdo);
        $this->repository = new PostgresFleetRepository($pdo);
    }

    public function testSaveAndGetFleet(): void
    {
        $fleet = Fleet::create(new UserId('user-1'));
        $this->repository->save($fleet);

        $loaded = $this->repository->get($fleet->id());

        self::assertSame((string) $fleet->id(), (string) $loaded->id());
        self::assertSame('user-1', (string) $loaded->ownerId());
    }

    public function testRegisterVehiclePersists(): void
    {
        $fleet = Fleet::create(new UserId('user-1'));
        $vehicle = new VehiclePlateNumber('AB-123-CD');
        $fleet->registerVehicle($vehicle);
        $this->repository->save($fleet);

        $loaded = $this->repository->get($fleet->id());

        self::assertTrue($loaded->hasVehicle($vehicle));
    }

    public function testParkVehiclePersists(): void
    {
        $fleet = Fleet::create(new UserId('user-1'));
        $vehicle = new VehiclePlateNumber('AB-123-CD');
        $location = new Location(48.8566, 2.3522, 35.0);
        $fleet->registerVehicle($vehicle);
        $fleet->parkVehicle($vehicle, $location);
        $this->repository->save($fleet);

        $loaded = $this->repository->get($fleet->id());

        self::assertTrue($location->equals($loaded->knownLocationOf($vehicle)));
    }

    public function testSameVehicleCanBelongToMultipleFleets(): void
    {
        $vehicle = new VehiclePlateNumber('AB-123-CD');
        $fleetA = Fleet::create(new UserId('user-1'));
        $fleetB = Fleet::create(new UserId('user-2'));
        $fleetA->registerVehicle($vehicle);
        $fleetB->registerVehicle($vehicle);
        $this->repository->save($fleetA);
        $this->repository->save($fleetB);

        self::assertTrue($this->repository->get($fleetA->id())->hasVehicle($vehicle));
        self::assertTrue($this->repository->get($fleetB->id())->hasVehicle($vehicle));
        self::assertNotSame((string) $fleetA->id(), (string) $fleetB->id());
    }

    public function testGetUnknownFleetThrows(): void
    {
        $this->expectException(\FleetParking\Domain\Exception\FleetNotFound::class);
        $this->repository->get(new FleetId('unknown-fleet-id'));
    }
}
