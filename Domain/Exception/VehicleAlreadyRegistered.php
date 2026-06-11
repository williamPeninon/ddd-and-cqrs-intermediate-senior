<?php

declare(strict_types=1);

namespace FleetParking\Domain\Exception;

use DomainException;

final class VehicleAlreadyRegistered extends DomainException
{
    public static function create(): self
    {
        return new self('This vehicle has already been registered into this fleet.');
    }
}
