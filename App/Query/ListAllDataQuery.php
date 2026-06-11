<?php

declare(strict_types=1);

namespace FleetParking\App\Query;

final readonly class ListAllDataQuery
{
    public function __construct(public ?string $ownerId = null, public ?string $fleetId = null)
    {
    }
}
