<?php

declare(strict_types=1);

namespace FleetParking\App\Query;

use FleetParking\App\Query\ReadModel\FleetSummary;
use FleetParking\App\Query\ReadModel\FleetVehicleSummary;
use FleetParking\App\Query\ReadModel\VehicleLocationSummary;
use FleetParking\App\Query\ReadModel\VehicleSummary;

interface FleetReadRepository
{
    /** @return list<FleetSummary> */
    public function findAllFleets(?string $ownerId = null): array;

    /** @return list<VehicleSummary> */
    public function findAllVehicles(): array;

    /** @return list<FleetVehicleSummary> */
    public function findAllFleetVehicles(?string $fleetId = null): array;

    /** @return list<VehicleLocationSummary> */
    public function findAllVehicleLocations(?string $fleetId = null): array;
}
