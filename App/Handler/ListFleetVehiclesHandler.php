<?php

declare(strict_types=1);

namespace FleetParking\App\Handler;

use FleetParking\App\Query\FleetReadRepository;
use FleetParking\App\Query\ListFleetVehiclesQuery;
use FleetParking\App\Query\ReadModel\FleetVehicleSummary;

final readonly class ListFleetVehiclesHandler
{
    public function __construct(private FleetReadRepository $fleets)
    {
    }

    /** @return list<FleetVehicleSummary> */
    public function __invoke(ListFleetVehiclesQuery $query): array
    {
        return $this->fleets->findAllFleetVehicles($query->fleetId);
    }
}
