<?php

declare(strict_types=1);

namespace FleetParking\Domain\Fleet;

use InvalidArgumentException;

final readonly class UserId
{
    public function __construct(public string $value)
    {
        if (trim($value) === '') {
            throw new InvalidArgumentException('User id cannot be empty.');
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
