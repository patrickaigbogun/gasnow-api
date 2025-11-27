<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use App\Models\Gender;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GenderController extends Controller
{
    public function index(): JsonResponse
    {
        $items = Gender::all();

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
            'name' => ['required', 'string', 'max:100', 'unique:genders,name'],
        ]);
        $item = Gender::create($validated);

        return response()->json(['data' => $item], 201);
    }

    public function update(Request $request, Gender $gender): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:genders,name,' . $gender->id],
        ]);
        $gender->update($validated);

        return response()->json(['data' => $gender]);
    }

    public function destroy(Gender $gender): JsonResponse
    {
        $gender->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
