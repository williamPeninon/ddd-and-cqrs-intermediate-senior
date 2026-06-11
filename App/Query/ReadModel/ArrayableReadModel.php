<?php

declare(strict_types=1);

namespace FleetParking\App\Query\ReadModel;

interface ArrayableReadModel
{
    /** @return array<string, mixed> */
    public function toArray(): array;
}
