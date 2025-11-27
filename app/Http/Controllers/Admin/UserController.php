<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Bouncer;

class UserController extends Controller
{

    protected function ensureSystemAdmin(): void
    {
        if (! auth()->user()?->isAn('system-admin')) {
            abort(403, 'Forbidden');
        }
    }

    public function store(Request $request): JsonResponse
    {
        $this->ensureSystemAdmin();

        $data = $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'staff_id' => 'nullable|integer',
            'roles' => 'required|array|min:1',
            'roles.*' => 'string|exists:roles,name',
            'primary_role' => 'required|string',
        ]);

        if (! in_array($data['primary_role'], $data['roles'], true)) {
            return response()->json([
                'message' => 'Primary role must be one of the roles array',
            ], 422);
        }

        DB::transaction(function () use ($data, &$user) {
            $user = User::create([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'active' => true,
            ]);

            foreach ($data['roles'] as $roleName) {
                Bouncer::assign($roleName)->to($user);
            }

            $primaryRoleModel = Bouncer::role()->where('name', $data['primary_role'])->first();
            if ($primaryRoleModel) {
                $user->primary_role_id = $primaryRoleModel->id;
                $user->save();
            }
        });

        return response()->json([
            'message' => 'User created',
            'data' => $user->load('primaryRole'),
        ], 201);
    }
}
