<?php

declare(strict_types=1);

namespace FleetParking\App\Handler;

use FleetParking\App\Query\FleetReadRepository;
use FleetParking\App\Query\ListVehiclesQuery;
use FleetParking\App\Query\ReadModel\VehicleSummary;

final readonly class ListVehiclesHandler
{
    public function __construct(private FleetReadRepository $fleets)
    {
    }

    /** @return list<VehicleSummary> */
    public function __invoke(ListVehiclesQuery $query): array
    {
        return $this->fleets->findAllVehicles();
    }
}
