<?php

declare(strict_types=1);

namespace FleetParking\App\Handler;

use FleetParking\App\Query\FleetReadRepository;
use FleetParking\App\Query\ListAllDataQuery;

final readonly class ListAllDataHandler
{
    public function __construct(private FleetReadRepository $fleets)
    {
    }

    /** @return array<string, list<array<string, float|string|null>>> */
    public function __invoke(ListAllDataQuery $query): array
    {
        return [
            'fleets' => array_map(
                static fn ($item) => $item->toArray(),
                $this->fleets->findAllFleets($query->ownerId),
            ),
            'vehicles' => array_map(
                static fn ($item) => $item->toArray(),
                $this->fleets->findAllVehicles(),
            ),
            'fleetVehicles' => array_map(
                static fn ($item) => $item->toArray(),
                $this->fleets->findAllFleetVehicles($query->fleetId),
            ),
            'parkings' => array_map(
                static fn ($item) => $item->toArray(),
                $this->fleets->findAllVehicleLocations($query->fleetId),
            ),
            'positions' => array_map(
                static fn ($item) => $item->toArray(),
                $this->fleets->findAllVehicleLocations($query->fleetId),
            ),
        ];
    }
}
