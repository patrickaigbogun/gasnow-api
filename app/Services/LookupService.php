<?php

namespace App\Services;

use App\Models\CylinderSize;
use App\Models\PurchaseKg;
use App\Models\DeliveryTime;
use Illuminate\Support\Collection;

class LookupService
{
    protected array $map = [
        'cylinder_sizes' => CylinderSize::class,
        'purchase_kgs'   => PurchaseKg::class,
        'delivery_times' => DeliveryTime::class,
    ];

    public function getMany(array $keys): array
    {
        $result = [];

        foreach ($keys as $key) {
            if (! isset($this->map[$key])) {
                continue;
            }

            $model = $this->map[$key];
            $items = $model::query()->get();

            $result[$key] = $items;
        }

        return $result;
    }

    public function getOne(string $key): ?Collection
    {
        $data = $this->getMany([$key]);

        return $data[$key] ?? null;
    }
}
