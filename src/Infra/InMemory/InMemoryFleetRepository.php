<?php

declare(strict_types=1);

namespace FleetParking\Infra\InMemory;

use FleetParking\Domain\Exception\FleetNotFound;
use FleetParking\Domain\Fleet\Fleet;
use FleetParking\Domain\Fleet\FleetId;
use FleetParking\Domain\Fleet\FleetRepository;

final class InMemoryFleetRepository implements FleetRepository
{
    /** @var array<string, Fleet> */
    private array $fleets = [];

    public function save(Fleet $fleet): void
    {
        $this->fleets[(string) $fleet->id()] = $fleet;
    }

    public function get(FleetId $id): Fleet
    {
        return $this->fleets[(string) $id] ?? throw FleetNotFound::withId((string) $id);
    }
}
