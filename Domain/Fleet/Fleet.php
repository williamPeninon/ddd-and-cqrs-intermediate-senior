<?php

declare(strict_types=1);

namespace FleetParking\Domain\Fleet;

use FleetParking\Domain\Exception\VehicleAlreadyParkedAtLocation;
use FleetParking\Domain\Exception\VehicleAlreadyRegistered;
use FleetParking\Domain\Exception\VehicleNotRegistered;
use FleetParking\Domain\Location\Location;
use FleetParking\Domain\Vehicle\VehiclePlateNumber;

final class Fleet
{
    /** @var array<string, VehiclePlateNumber> */
    private array $vehicles = [];

    /** @var array<string, Location> */
    private array $locations = [];

    public function __construct(
        private readonly FleetId $id,
        private readonly UserId $ownerId,
    ) {
    }

    public static function create(UserId $ownerId): self
    {
        return new self(FleetId::generate(), $ownerId);
    }

    public function id(): FleetId
    {
        return $this->id;
    }

    public function ownerId(): UserId
    {
        return $this->ownerId;
    }

    public function registerVehicle(VehiclePlateNumber $plateNumber): void
    {
        $key = (string) $plateNumber;
        if (isset($this->vehicles[$key])) {
            throw VehicleAlreadyRegistered::create();
        }

        $this->vehicles[$key] = $plateNumber;
    }

    public function parkVehicle(VehiclePlateNumber $plateNumber, Location $location): void
    {
        $key = (string) $plateNumber;
        if (!isset($this->vehicles[$key])) {
            throw VehicleNotRegistered::create();
        }

        if (isset($this->locations[$key]) && $this->locations[$key]->equals($location)) {
            throw VehicleAlreadyParkedAtLocation::create();
        }

        $this->locations[$key] = $location;
    }

    public function hasVehicle(VehiclePlateNumber $plateNumber): bool
    {
        return isset($this->vehicles[(string) $plateNumber]);
    }

    public function knownLocationOf(VehiclePlateNumber $plateNumber): ?Location
    {
        return $this->locations[(string) $plateNumber] ?? null;
    }

    /** @return list<VehiclePlateNumber> */
    public function vehicles(): array
    {
        return array_values($this->vehicles);
    }

    /** @return array<string, Location> */
    public function locations(): array
    {
        return $this->locations;
    }

    /**
     * Rehydrate a fleet from persistence without replaying domain rules.
     *
     * @param list<string> $vehicles
     * @param array<string, array{lat: float, lng: float, alt: float|null}> $locations
     */
    public static function reconstitute(FleetId $id, UserId $ownerId, array $vehicles, array $locations): self
    {
        $fleet = new self($id, $ownerId);
        foreach ($vehicles as $plateNumber) {
            $fleet->vehicles[(string) new VehiclePlateNumber($plateNumber)] = new VehiclePlateNumber($plateNumber);
        }
        foreach ($locations as $plateNumber => $location) {
            $fleet->locations[(string) new VehiclePlateNumber($plateNumber)] = new Location(
                $location['lat'],
                $location['lng'],
                $location['alt'],
            );
        }

        return $fleet;
    }
}
