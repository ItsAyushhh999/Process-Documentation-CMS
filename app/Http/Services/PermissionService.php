<?php

namespace App\Http\Services;

use App\Models\PermissionUser;
use App\Models\User;

class PermissionService
{
    public function syncPermissions(User $user, array $permissions)
    {
        try {
            $userPermissions = [];
            $permissionsData = [];

            foreach ($permissions as $permission) {
                list($permissionId, $projectId) = explode(':', $permission);

                $user->userPermissions->contains(function ($uPermission) use ($permissionId, $projectId) {
                    return $uPermission->project_id == $projectId && $uPermission->permission_id == $permissionId;
                }) ? $permissionsData[] = [$permissionId, $projectId] : $userPermissions[] = ['user_id' => $user->id, 'permission_id' => $permissionId, 'project_id' => $projectId];
            }

            $query = PermissionUser::where('user_id', $user->id);
            foreach ($permissionsData as $data) {
                $query->where(function ($innerQuery) use ($data) {
                    $innerQuery->where('permission_id', '!=', $data[0])
                        ->orWhere('project_id', '!=', $data[1]);
                });
            }
            $query->delete();

            PermissionUser::insert($userPermissions);

        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param $projectId
     * @param $permissionId
     * @return bool
     */
    public function isPermitted($projectId, $permissionId): bool
    {
        $userId = auth()->id();

        return PermissionUser::where([
            ['user_id', '=', $userId],
            ['project_id', '=', $projectId],
            ['permission_id', '=', $permissionId],
        ])->exists();
    }

    /**
     * @param $projectId
     * @return mixed
     */
    public function permissionProject($projectId)
    {
        $userId = auth()->id();

        return PermissionUser::where([
            ['user_id', '=', $userId],
            ['project_id', '=', $projectId],
        ])->get();
    }

    public function permissionNameByProject($projectId): array
    {
        $userId = auth()->id();

        return PermissionUser::with('permission:id,name')->where([
            ['user_id', '=', $userId],
            ['project_id', '=', $projectId],
        ])->get()->map(function ($permission) {
            return strtoupper($permission->permission->name);
        })->toArray();
    }
}
