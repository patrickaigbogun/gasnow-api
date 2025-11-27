<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use App\Models\ComplaintCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ComplaintCategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $items = ComplaintCategory::all();

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
            'name' => ['required', 'string', 'max:255', 'unique:complaint_categories,name'],
        ]);
        $item = ComplaintCategory::create($validated);

        return response()->json(['data' => $item], 201);
    }

    public function update(Request $request, ComplaintCategory $complaint_category): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:complaint_categories,name,' . $complaint_category->id],
        ]);
        $complaint_category->update($validated);

        return response()->json(['data' => $complaint_category]);
    }

    public function destroy(ComplaintCategory $complaint_category): JsonResponse
    {
        $complaint_category->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
