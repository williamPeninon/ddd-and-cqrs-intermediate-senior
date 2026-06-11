<?php

declare(strict_types=1);

namespace FleetParking\Domain\Fleet;

interface FleetRepository
{
    public function save(Fleet $fleet): void;

    public function get(FleetId $id): Fleet;
}
