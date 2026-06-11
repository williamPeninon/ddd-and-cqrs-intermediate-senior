<?php

declare(strict_types=1);

namespace FleetParking\App\Cli;

use FleetParking\App\Query\ReadModel\ArrayableReadModel;

final class JsonOutput
{
    /** @param list<ArrayableReadModel>|array<string, mixed> $data */
    public static function encode(array $data): string
    {
        $normalized = self::normalize($data);

        return json_encode($normalized, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR) . PHP_EOL;
    }

    /**
     * @param list<ArrayableReadModel>|array<string, mixed> $data
     * @return array<int|string, mixed>
     */
    private static function normalize(array $data): array
    {
        if ($data === []) {
            return [];
        }

        if (array_is_list($data) && $data[0] instanceof ArrayableReadModel) {
            return array_map(static fn (ArrayableReadModel $item): array => $item->toArray(), $data);
        }

        $normalized = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                /** @var array<string, mixed> $value */
                $normalized[$key] = self::normalize($value);
                continue;
            }

            if ($value instanceof ArrayableReadModel) {
                $normalized[$key] = $value->toArray();
                continue;
            }

            $normalized[$key] = $value;
        }

        return $normalized;
    }
}
