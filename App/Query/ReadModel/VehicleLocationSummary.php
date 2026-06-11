<?php

declare(strict_types=1);

namespace FleetParking\App\Query\ReadModel;

final readonly class VehicleLocationSummary implements ArrayableReadModel
{
    public function __construct(
        public string $fleetId,
        public string $plateNumber,
        public float $latitude,
        public float $longitude,
        public ?float $altitude,
        public string $localizedAt,
    ) {
    }

    /** @return array<string, float|string|null> */
    public function toArray(): array
    {
        return [
            'fleetId' => $this->fleetId,
            'plateNumber' => $this->plateNumber,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'altitude' => $this->altitude,
            'localizedAt' => $this->localizedAt,
        ];
    }
}
