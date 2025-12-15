<?php

namespace App\Services;

use App\Models\CylinderSize;
use App\Models\PurchaseKg;
use App\Models\DeliveryTime;
use App\Models\ComplaintCategory;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Gender;
use App\Models\Status;
use App\Models\PaymentType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class LookupService
{
    /**
     * Map of lookup types to their model classes and validation rules
     */
    protected array $lookups = [
        'cylinder-sizes' => [
            'model' => CylinderSize::class,
            'table' => 'cylinder_sizes',
            'rules' => ['name' => 'required|string|max:200'],
            'public' => true, // Available to customers
        ],
        'purchase-kgs' => [
            'model' => PurchaseKg::class,
            'table' => 'purchase_kgs',
            'rules' => ['name' => 'required|string|max:200'],
            'public' => true,
        ],
        'delivery-times' => [
            'model' => DeliveryTime::class,
            'table' => 'delivery_times',
            'rules' => ['name' => 'required|string|max:200'],
            'public' => true,
        ],
        'complaint-categories' => [
            'model' => ComplaintCategory::class,
            'table' => 'complaint_categories',
            'rules' => ['name' => 'required|string|max:200'],
            'public' => false,
        ],
        'departments' => [
            'model' => Department::class,
            'table' => 'departments',
            'rules' => [
                'name' => 'required|string|max:200',
                'description' => 'nullable|string|max:500',
            ],
            'public' => false,
        ],
        'designations' => [
            'model' => Designation::class,
            'table' => 'designations',
            'rules' => ['name' => 'required|string|max:200'],
            'public' => false,
        ],
        'genders' => [
            'model' => Gender::class,
            'table' => 'genders',
            'rules' => ['name' => 'required|string|max:200'],
            'public' => false,
        ],
        'statuses' => [
            'model' => Status::class,
            'table' => 'statuses',
            'rules' => [
                'name' => 'required|string|max:200',
                'description' => 'nullable|string|max:500',
            ],
            'public' => false,
        ],
        'payment-types' => [
            'model' => PaymentType::class,
            'table' => 'payment_types',
            'rules' => ['name' => 'required|string|max:200'],
            'public' => false,
        ],
    ];

    /**
     * Get all available lookup types
     */
    public function getAvailableTypes(): array
    {
        return array_keys($this->lookups);
    }

    /**
     * Get public lookup types (available to customers)
     */
    public function getPublicTypes(): array
    {
        return array_keys(array_filter($this->lookups, fn($config) => $config['public']));
    }

    /**
     * Check if a lookup type exists
     */
    public function exists(string $type): bool
    {
        return isset($this->lookups[$type]);
    }

    /**
     * Check if a lookup type is public
     */
    public function isPublic(string $type): bool
    {
        return $this->lookups[$type]['public'] ?? false;
    }

    /**
     * Get the model class for a lookup type
     */
    public function getModelClass(string $type): ?string
    {
        return $this->lookups[$type]['model'] ?? null;
    }

    /**
     * Get validation rules for a lookup type
     */
    public function getValidationRules(string $type, ?int $excludeId = null): array
    {
        $config = $this->lookups[$type] ?? null;
        if (!$config) {
            return [];
        }

        $rules = $config['rules'];
        $table = $config['table'];

        // Add unique constraint for name field
        if (isset($rules['name'])) {
            $unique = "unique:{$table},name";
            if ($excludeId) {
                $unique .= ",{$excludeId}";
            }
            $rules['name'] .= "|{$unique}";
        }

        return $rules;
    }

    /**
     * Get all items for a lookup type
     */
    public function all(string $type): Collection
    {
        $modelClass = $this->getModelClass($type);
        if (!$modelClass) {
            return collect();
        }

        return $modelClass::all();
    }

    /**
     * Find a single item by ID
     */
    public function find(string $type, int $id): ?Model
    {
        $modelClass = $this->getModelClass($type);
        if (!$modelClass) {
            return null;
        }

        return $modelClass::find($id);
    }

    /**
     * Create a new lookup item
     */
    public function create(string $type, array $data): ?Model
    {
        $modelClass = $this->getModelClass($type);
        if (!$modelClass) {
            return null;
        }

        return $modelClass::create($data);
    }

    /**
     * Update a lookup item
     */
    public function update(string $type, int $id, array $data): ?Model
    {
        $item = $this->find($type, $id);
        if (!$item) {
            return null;
        }

        $item->update($data);
        return $item->fresh();
    }

    /**
     * Delete a lookup item
     */
    public function delete(string $type, int $id): bool
    {
        $item = $this->find($type, $id);
        if (!$item) {
            return false;
        }

        return $item->delete();
    }

    /**
     * Get multiple lookup types at once (for combined lookups endpoint)
     */
    public function getMany(array $types): array
    {
        $result = [];

        foreach ($types as $type) {
            if ($this->exists($type)) {
                // Convert type to snake_case key for response
                $key = str_replace('-', '_', $type);
                $result[$key] = $this->all($type);
            }
        }

        return $result;
    }

    /**
     * Get a single lookup type (alias for all)
     */
    public function getOne(string $type): ?Collection
    {
        if (!$this->exists($type)) {
            return null;
        }

        return $this->all($type);
    }
}
