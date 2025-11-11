<?php

namespace App\Support;

class PermissionScopeMapper
{
    protected static array $scopes = [
        'purchases' => ['create', 'read', 'update', 'delete'],
        'billing' => ['create', 'read', 'update', 'delete'],
        'profiles' => ['read', 'update', 'delete'],
        'audit' => ['read', 'update'],
        'password' => ['update'],
        'setup-admin' => ['create', 'read', 'update', 'delete'],
        'setup-system' => ['create', 'read', 'update', 'delete'],
    ];

    public static function map(array $abilities): array
    {
        $result = [];

        foreach (self::$scopes as $scope => $actions) {
            $result[$scope] = [];

            foreach ($actions as $action) {
                $permissionName = "{$action}-{$scope}";
                $result[$scope][$action] = in_array($permissionName, $abilities);
            }
        }

        return $result;
    }
}
