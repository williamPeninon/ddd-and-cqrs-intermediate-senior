<?php

declare(strict_types=1);

namespace FleetParking\App\Handler;

use FleetParking\App\Command\ParkVehicleCommand;
use FleetParking\Domain\Fleet\FleetId;
use FleetParking\Domain\Fleet\FleetRepository;
use FleetParking\Domain\Location\Location;
use FleetParking\Domain\Vehicle\VehiclePlateNumber;

final readonly class ParkVehicleHandler
{
    public function __construct(private FleetRepository $fleets)
    {
    }

    public function __invoke(ParkVehicleCommand $command): void
    {
        $fleet = $this->fleets->get(new FleetId($command->fleetId));
        $fleet->parkVehicle(
            new VehiclePlateNumber($command->vehiclePlateNumber),
            new Location($command->latitude, $command->longitude, $command->altitude),
        );
        $this->fleets->save($fleet);
    }
}
