<?php

declare(strict_types=1);

namespace FleetParking\App\Handler;

use FleetParking\App\Command\RegisterVehicleCommand;
use FleetParking\Domain\Fleet\FleetId;
use FleetParking\Domain\Fleet\FleetRepository;
use FleetParking\Domain\Vehicle\VehiclePlateNumber;

final readonly class RegisterVehicleHandler
{
    public function __construct(private FleetRepository $fleets)
    {
    }

    public function __invoke(RegisterVehicleCommand $command): void
    {
        $fleet = $this->fleets->get(new FleetId($command->fleetId));
        $fleet->registerVehicle(new VehiclePlateNumber($command->vehiclePlateNumber));
        $this->fleets->save($fleet);
    }
}
