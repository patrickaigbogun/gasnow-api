<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use App\Services\LookupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LookupController extends Controller
{
    public function __construct(
        protected LookupService $lookupService
    ) {}

    /**
     * Get all available lookup types
     */
    public function types(): JsonResponse
    {
        return response()->json([
            'data' => $this->lookupService->getAvailableTypes(),
        ]);
    }

    /**
     * List all items for a lookup type
     */
    public function index(string $type): JsonResponse
    {
        if (!$this->lookupService->exists($type)) {
            return response()->json([
                'message' => "Unknown lookup type: {$type}",
                'available_types' => $this->lookupService->getAvailableTypes(),
            ], 404);
        }

        $items = $this->lookupService->all($type);

        return response()->json([
            'data' => $items,
            'meta' => [
                'type' => $type,
                'count' => $items->count(),
            ],
        ]);
    }

    /**
     * Get a single lookup item
     */
    public function show(string $type, int $id): JsonResponse
    {
        if (!$this->lookupService->exists($type)) {
            return response()->json([
                'message' => "Unknown lookup type: {$type}",
            ], 404);
        }

        $item = $this->lookupService->find($type, $id);

        if (!$item) {
            return response()->json([
                'message' => "Item not found",
            ], 404);
        }

        return response()->json([
            'data' => $item,
        ]);
    }

    /**
     * Create a new lookup item
     */
    public function store(Request $request, string $type): JsonResponse
    {
        if (!$this->lookupService->exists($type)) {
            return response()->json([
                'message' => "Unknown lookup type: {$type}",
                'available_types' => $this->lookupService->getAvailableTypes(),
            ], 404);
        }

        $rules = $this->lookupService->getValidationRules($type);
        $validated = $request->validate($rules);

        $item = $this->lookupService->create($type, $validated);

        return response()->json([
            'data' => $item,
            'message' => 'Created successfully',
        ], 201);
    }

    /**
     * Update a lookup item
     */
    public function update(Request $request, string $type, int $id): JsonResponse
    {
        if (!$this->lookupService->exists($type)) {
            return response()->json([
                'message' => "Unknown lookup type: {$type}",
            ], 404);
        }

        $existingItem = $this->lookupService->find($type, $id);
        if (!$existingItem) {
            return response()->json([
                'message' => "Item not found",
            ], 404);
        }

        $rules = $this->lookupService->getValidationRules($type, $id);
        $validated = $request->validate($rules);

        $item = $this->lookupService->update($type, $id, $validated);

        return response()->json([
            'data' => $item,
            'message' => 'Updated successfully',
        ]);
    }

    /**
     * Delete a lookup item
     */
    public function destroy(string $type, int $id): JsonResponse
    {
        if (!$this->lookupService->exists($type)) {
            return response()->json([
                'message' => "Unknown lookup type: {$type}",
            ], 404);
        }

        $existingItem = $this->lookupService->find($type, $id);
        if (!$existingItem) {
            return response()->json([
                'message' => "Item not found",
            ], 404);
        }

        $this->lookupService->delete($type, $id);

        return response()->json([
            'message' => 'Deleted successfully',
        ]);
    }
}
