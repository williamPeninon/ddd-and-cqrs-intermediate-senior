<?php

declare(strict_types=1);

namespace FleetParking\App\Query;

final readonly class ListFleetVehiclesQuery
{
    public function __construct(public ?string $fleetId = null)
    {
    }
}
