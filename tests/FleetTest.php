<?php

declare(strict_types=1);

namespace FleetParking\Tests;

use FleetParking\Domain\Exception\VehicleAlreadyRegistered;
use FleetParking\Domain\Fleet\Fleet;
use FleetParking\Domain\Fleet\UserId;
use FleetParking\Domain\Location\Location;
use FleetParking\Domain\Vehicle\VehiclePlateNumber;
use PHPUnit\Framework\TestCase;

final class FleetTest extends TestCase
{
    public function testRegisterVehicle(): void
    {
        $fleet = Fleet::create(new UserId('user-1'));
        $vehicle = new VehiclePlateNumber('AB-123-CD');

        $fleet->registerVehicle($vehicle);

        self::assertTrue($fleet->hasVehicle($vehicle));
    }

    public function testCannotRegisterSameVehicleTwice(): void
    {
        $fleet = Fleet::create(new UserId('user-1'));
        $vehicle = new VehiclePlateNumber('AB-123-CD');
        $fleet->registerVehicle($vehicle);

        $this->expectException(VehicleAlreadyRegistered::class);
        $fleet->registerVehicle($vehicle);
    }

    public function testParkVehicle(): void
    {
        $fleet = Fleet::create(new UserId('user-1'));
        $vehicle = new VehiclePlateNumber('AB-123-CD');
        $location = new Location(48.8566, 2.3522);

        $fleet->registerVehicle($vehicle);
        $fleet->parkVehicle($vehicle, $location);

        self::assertTrue($location->equals($fleet->knownLocationOf($vehicle)));
    }
}
