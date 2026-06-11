<?php

declare(strict_types=1);

namespace FleetParking\App\Command;

final readonly class ParkVehicleCommand
{
    public function __construct(
        public string $fleetId,
        public string $vehiclePlateNumber,
        public float $latitude,
        public float $longitude,
        public ?float $altitude = null,
    ) {
    }
}
