<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\PersonalAccessToken;
use Bouncer;

class RoleController extends Controller
{
    public function __construct()
    {

    }

    protected function ensureSystemAdmin(String $token)
    {

        $personalToken = PersonalAccessToken::findToken($token);
        if (! $personalToken) {
            return response()->json(['message' => 'Invalid token'], 401);
        }
        $user = $personalToken->tokenable;

        if (! $user->isAn('system-admin')) {
            abort(403, 'Forbidden');
        }
    }

    public function index(Request $request): JsonResponse
    {
           $token = $request->bearerToken();
        if (! $token) {
            return response()->json(['message' => 'Missing token'], 400);
        } 
        $this->ensureSystemAdmin($token);

        $roles = Bouncer::role()->with('abilities')->get();

        return response()->json([
            'data' => $roles->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'title' => $role->title,
                    'abilities' => $role->abilities->pluck('name')->values(),
                ];
            }),
        ]);
    }

    public function abilities(Request $request): JsonResponse
    {
        $token = $request->bearerToken();
        if (! $token) {
            return response()->json(['message' => 'Missing token'], 400);
        }
        $this->ensureSystemAdmin($token);

        $abilities = Bouncer::ability()->get(['id', 'name', 'title']);

        return response()->json([
            'data' => $abilities,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $token = $request->bearerToken();
        if (! $token) {
            return response()->json(['message' => 'Missing token'], 400);
        }
        $this->ensureSystemAdmin($token);   
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'title' => 'nullable|string|max:255',
            'abilities' => 'required|array|min:1',
            'abilities.*' => 'string|exists:abilities,name',
        ]);

        DB::transaction(function () use ($data, &$role) {
            $role = Bouncer::role()->create([
                'name' => $data['name'],
                'title' => $data['title'] ?? null,
            ]);

            Bouncer::allow($role)->to($data['abilities']);
        });

        return response()->json([
            'message' => 'Role created',
            'data' => [
                'id' => $role->id,
                'name' => $role->name,
                'title' => $role->title,
                'abilities' => $role->abilities->pluck('name'),
            ],
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $token = $request->bearerToken();
        if (! $token) {
            return response()->json(['message' => 'Missing token'], 400);
        }
        $this->ensureSystemAdmin($token);

        $role = Bouncer::role()->findOrFail($id);

        $data = $request->validate([
            'title' => 'nullable|string|max:255',
            'abilities' => 'required|array|min:1',
            'abilities.*' => 'string|exists:abilities,name',
        ]);

        DB::transaction(function () use ($role, $data) {
            $role->update([
                'title' => $data['title'] ?? $role->title,
            ]);

            foreach ($role->abilities as $ability) {
                Bouncer::disallow($role)->to($ability->name);
            }

            Bouncer::allow($role)->to($data['abilities']);
        });

        return response()->json([
            'message' => 'Role updated',
            'data' => [
                'id' => $role->id,
                'name' => $role->name,
                'title' => $role->title,
                'abilities' => $role->refresh()->abilities->pluck('name'),
            ],
        ]);
    }
}
