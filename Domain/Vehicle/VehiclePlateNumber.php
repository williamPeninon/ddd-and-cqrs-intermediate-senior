<?php

declare(strict_types=1);

namespace FleetParking\Domain\Vehicle;

use InvalidArgumentException;

final readonly class VehiclePlateNumber
{
    public function __construct(public string $value)
    {
        $value = trim($this->value);
        if ($value === '') {
            throw new InvalidArgumentException('Vehicle plate number cannot be empty.');
        }
    }

    public function equals(self $other): bool
    {
        return mb_strtoupper($this->value) === mb_strtoupper($other->value);
    }

    public function __toString(): string
    {
        return mb_strtoupper($this->value);
    }
}
