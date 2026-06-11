<?php

declare(strict_types=1);

namespace FleetParking\App\Handler;

use FleetParking\App\Command\CreateFleetCommand;
use FleetParking\Domain\Fleet\Fleet;
use FleetParking\Domain\Fleet\FleetRepository;
use FleetParking\Domain\Fleet\UserId;

final readonly class CreateFleetHandler
{
    public function __construct(private FleetRepository $fleets)
    {
    }

    public function __invoke(CreateFleetCommand $command): string
    {
        $fleet = Fleet::create(new UserId($command->userId));
        $this->fleets->save($fleet);

        return (string) $fleet->id();
    }
}
