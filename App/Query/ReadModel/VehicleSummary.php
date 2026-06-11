<?php

declare(strict_types=1);

namespace FleetParking\App\Query\ReadModel;

final readonly class VehicleSummary implements ArrayableReadModel
{
    public function __construct(
        public string $plateNumber,
    ) {
    }

    /** @return array<string, string> */
    public function toArray(): array
    {
        return [
            'plateNumber' => $this->plateNumber,
        ];
    }
}
