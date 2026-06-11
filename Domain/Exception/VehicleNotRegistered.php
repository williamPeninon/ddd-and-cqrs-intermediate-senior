<?php

declare(strict_types=1);

namespace FleetParking\Domain\Exception;

use DomainException;

final class VehicleNotRegistered extends DomainException
{
    public static function create(): self
    {
        return new self('This vehicle is not registered into this fleet.');
    }
}
