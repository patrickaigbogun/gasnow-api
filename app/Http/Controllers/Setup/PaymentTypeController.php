<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use App\Models\PaymentType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentTypeController extends Controller
{
    public function index(): JsonResponse
    {
        $items = PaymentType::all();

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
            'name' => ['required', 'string', 'max:255', 'unique:payment_types,name'],
        ]);
        $item = PaymentType::create($validated);

        return response()->json(['data' => $item], 201);
    }

    public function update(Request $request, PaymentType $payment_type): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:payment_types,name,' . $payment_type->id],
        ]);
        $payment_type->update($validated);

        return response()->json(['data' => $payment_type]);
    }

    public function destroy(PaymentType $payment_type): JsonResponse
    {
        $payment_type->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
