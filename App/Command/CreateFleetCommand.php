<?php

declare(strict_types=1);

namespace FleetParking\App\Command;

final readonly class CreateFleetCommand
{
    public function __construct(public string $userId)
    {
    }
}
