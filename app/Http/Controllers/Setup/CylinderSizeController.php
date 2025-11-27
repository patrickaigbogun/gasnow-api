<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use App\Models\CylinderSize;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CylinderSizeController extends Controller
{
    public function index(): JsonResponse
    {
        $items = CylinderSize::all();

        return response()->json([
            'data' => $items,
            'meta' => [
                'count' => $items->count(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200', 'unique:cylinder_sizes,name'],
        ]);
        $item = CylinderSize::create($validated);

        return response()->json(['data' => $item], 201);
    }

    public function update(Request $request, CylinderSize $cylinder_size): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200', 'unique:cylinder_sizes,name,' . $cylinder_size->id],
        ]);
        $cylinder_size->update($validated);

        return response()->json(['data' => $cylinder_size]);
    }

    public function destroy(CylinderSize $cylinder_size): JsonResponse
    {
        $cylinder_size->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
