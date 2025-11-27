<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use App\Models\PurchaseKg;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PurchaseKgController extends Controller
{
    public function index(): JsonResponse
    {
        $items = PurchaseKg::all();

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
            'name' => ['required', 'string', 'max:200', 'unique:purchase_kgs,name'],
        ]);
        $item = PurchaseKg::create($validated);

        return response()->json(['data' => $item], 201);
    }

    public function update(Request $request, PurchaseKg $purchase_kgs): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:200', 'unique:purchase_kgs,name,' . $purchase_kgs->id],
        ]);
        $purchase_kgs->update($validated);

        return response()->json(['data' => $purchase_kgs]);
    }

    public function destroy(PurchaseKg $purchase_kgs): JsonResponse
    {
        $purchase_kgs->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
