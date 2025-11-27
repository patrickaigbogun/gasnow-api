<?php

namespace App\Http\Controllers;

use App\Services\LookupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PurchaseLookupController extends Controller
{
    public function __construct(protected LookupService $lookups)
    {
    }

    public function index(Request $request): JsonResponse
    {
        if(!auth()->$user){
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $data = $this->lookups->getMany([
            'cylinder_sizes',
            'purchase_kgs',
            'delivery_times',
        ]);

        return response()->json([
            'data' => $data,
            'meta' => [
                'count' => [
                    'cylinder_sizes' => $data['cylinder_sizes']->count(),
                    'purchase_kgs'   => $data['purchase_kgs']->count(),
                    'delivery_times' => $data['delivery_times']->count(),
                ],
            ],
        ]);
    }
}
