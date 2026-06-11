<?php

declare(strict_types=1);

namespace FleetParking\App\Query;

final readonly class ListFleetsQuery
{
    public function __construct(public ?string $ownerId = null)
    {
    }
}
