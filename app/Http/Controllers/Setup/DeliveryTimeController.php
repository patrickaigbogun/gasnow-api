<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use App\Models\DeliveryTime;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeliveryTimeController extends Controller
{
    public function index(): JsonResponse
    {
        $items = DeliveryTime::all();

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
            'name' => ['required', 'string', 'max:200', 'unique:delivery_times,name'],
        ]);
        $item = DeliveryTime::create($validated);

        return response()->json(['data' => $item], 201);
    }

    public function update(Request $request, DeliveryTime $delivery_time): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200', 'unique:delivery_times,name,' . $delivery_time->id],
        ]);
        $delivery_time->update($validated);

        return response()->json(['data' => $delivery_time]);
    }

    public function destroy(DeliveryTime $delivery_time): JsonResponse
    {
        $delivery_time->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
