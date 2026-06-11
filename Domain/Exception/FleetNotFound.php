<?php

declare(strict_types=1);

namespace FleetParking\Domain\Exception;

use RuntimeException;

final class FleetNotFound extends RuntimeException
{
    public static function withId(string $fleetId): self
    {
        return new self(sprintf('Fleet "%s" was not found.', $fleetId));
    }
}
