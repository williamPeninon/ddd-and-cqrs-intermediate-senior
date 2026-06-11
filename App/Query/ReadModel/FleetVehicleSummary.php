<?php

declare(strict_types=1);

namespace FleetParking\App\Query\ReadModel;

final readonly class FleetVehicleSummary implements ArrayableReadModel
{
    public function __construct(
        public string $fleetId,
        public string $plateNumber,
    ) {
    }

    /** @return array<string, string> */
    public function toArray(): array
    {
        return [
            'fleetId' => $this->fleetId,
            'plateNumber' => $this->plateNumber,
        ];
    }
}
