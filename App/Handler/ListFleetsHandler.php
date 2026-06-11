<?php

declare(strict_types=1);

namespace FleetParking\App\Handler;

use FleetParking\App\Query\FleetReadRepository;
use FleetParking\App\Query\ListFleetsQuery;
use FleetParking\App\Query\ReadModel\FleetSummary;

final readonly class ListFleetsHandler
{
    public function __construct(private FleetReadRepository $fleets)
    {
    }

    /** @return list<FleetSummary> */
    public function __invoke(ListFleetsQuery $query): array
    {
        return $this->fleets->findAllFleets($query->ownerId);
    }
}
