<?php

namespace App\Services;

use App\Models\User;
use Silber\Bouncer\BouncerFacade as Bouncer;
use App\Support\PermissionScopeMapper;

class SessionContext
{
    public static function build(User $user): array
    {
        $user->loadMissing('primaryRole');

        $roles = $user->getRoles();

        $abilities = $user->getAbilities()->pluck('name')->toArray();

        $permissions = PermissionScopeMapper::map($abilities);

        $primaryRoleName = $user->primaryRole?->name;

        return [
            'user' => $user->only(['id', 'username', 'email']),
            'primary_role' => $primaryRoleName,
            'roles' => $roles,
            'permissions' => $permissions,
        ];
    }
}
