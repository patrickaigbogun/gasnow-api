<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    public function index(): JsonResponse
    {
        $items = Designation::all();

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
            'name' => ['required', 'string', 'max:100', 'unique:designations,name'],
        ]);
        $item = Designation::create($validated);

        return response()->json(['data' => $item], 201);
    }

    public function update(Request $request, Designation $designation): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:designations,name,' . $designation->id],
        ]);
        $designation->update($validated);

        return response()->json(['data' => $designation]);
    }

    public function destroy(Designation $designation): JsonResponse
    {
        $designation->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
