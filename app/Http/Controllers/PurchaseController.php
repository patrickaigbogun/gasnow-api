<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseRequest;
use App\Http\Requests\UpdatePurchaseRequest;
use App\Models\Purchase;
use App\Services\PurchaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function __construct(
        protected PurchaseService $purchases
    ) {}

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $purchases = $this->purchases->list($user);

        return response()->json([
            'purchases' => $purchases,
        ]);
    }

    public function show(int $purchase): JsonResponse
    {
        $purchase = $this->purchases->find($purchase);

        return response()->json([
            'purchase' => $purchase,
        ]);
    }

    public function store(StorePurchaseRequest $request): JsonResponse
    {
        $user = $request->user();
        $purchase = $this->purchases->create($user, $request->validated());

        return response()->json([
            'purchase' => $purchase,
        ], 201);
    }

    public function update(UpdatePurchaseRequest $request, Purchase $purchase): JsonResponse
    {
        $purchase = $this->purchases->update($purchase, $request->validated());

        return response()->json([
            'purchase' => $purchase,
        ]);
    }

    public function destroy(Purchase $purchase): JsonResponse
    {
        $this->purchases->delete($purchase);

        return response()->json([
            'message' => 'Purchase deleted.',
        ]);
    }
}
