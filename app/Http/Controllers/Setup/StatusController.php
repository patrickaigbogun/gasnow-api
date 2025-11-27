<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use App\Models\Status;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    public function index(): JsonResponse
    {
        $items = Status::all();

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
            'name' => ['required', 'string', 'max:100', 'unique:statuses,name'],
        ]);
        $item = Status::create($validated);

        return response()->json(['data' => $item], 201);
    }

    public function update(Request $request, Status $status): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:statuses,name,' . $status->id],
        ]);
        $status->update($validated);

        return response()->json(['data' => $status]);
    }

    public function destroy(Status $status): JsonResponse
    {
        $status->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
