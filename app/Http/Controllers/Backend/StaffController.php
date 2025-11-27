<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Staff::with(['gender', 'department', 'designation', 'status'])->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'surname' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',

            'gender_id' => 'nullable|exists:genders,id',
            'email' => 'nullable|email|unique:staff,email',
            'phone_number' => 'nullable|string|max:50',

            'department_id' => 'nullable|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'status_id' => 'nullable|exists:status,id',
        ]);

        $staff = Staff::create($validated);

        return response()->json(['data' => $staff], 201);
    }

    public function update(Request $request, Staff $staff): JsonResponse
    {
        $validated = $request->validate([
            'surname' => 'sometimes|string|max:255',
            'first_name' => 'sometimes|string|max:255',
            'middle_name' => 'nullable|string|max:255',

            'gender_id' => 'nullable|exists:genders,id',
            'email' => "nullable|email|unique:staff,email,{$staff->id}",
            'phone_number' => 'nullable|string|max:50',

            'department_id' => 'nullable|exists:departments,id',
            'designation_id' => 'nullable|exists:designations,id',
            'status_id' => 'nullable|exists:status,id',
        ]);

        $staff->update($validated);

        return response()->json(['data' => $staff]);
    }
}
