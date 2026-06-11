<?php

declare(strict_types=1);

namespace FleetParking\App\Query\ReadModel;

final readonly class FleetSummary implements ArrayableReadModel
{
    public function __construct(
        public string $id,
        public string $ownerId,
        public string $createdAt,
    ) {
    }

    /** @return array<string, string> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'ownerId' => $this->ownerId,
            'createdAt' => $this->createdAt,
        ];
    }
}
