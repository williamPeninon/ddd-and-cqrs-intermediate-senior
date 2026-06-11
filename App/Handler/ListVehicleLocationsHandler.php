<?php

declare(strict_types=1);

namespace FleetParking\App\Handler;

use FleetParking\App\Query\FleetReadRepository;
use FleetParking\App\Query\ListVehicleLocationsQuery;
use FleetParking\App\Query\ReadModel\VehicleLocationSummary;

final readonly class ListVehicleLocationsHandler
{
    public function __construct(private FleetReadRepository $fleets)
    {
    }

    /** @return list<VehicleLocationSummary> */
    public function __invoke(ListVehicleLocationsQuery $query): array
    {
        return $this->fleets->findAllVehicleLocations($query->fleetId);
    }
}
