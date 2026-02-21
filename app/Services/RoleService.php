<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\RoleModel;
use App\Models\UserRoleModel;

class RoleService
{
    public function getRolesForUser(int $userId): array
    {
        $rows = (new UserRoleModel())
            ->select('roles.name')
            ->join('roles', 'roles.id = user_roles.role_id')
            ->where('user_id', $userId)
            ->findAll();

        return array_map(static fn(array $row): string => $row['name'], $rows);
    }

    public function hasAnyRole(int $userId, array $allowedRoles): bool
    {
        $roles = $this->getRolesForUser($userId);

        return [] !== array_intersect($roles, $allowedRoles);
    }

    public function assignRole(int $userId, string $roleName): void
    {
        $role = (new RoleModel())->where('name', $roleName)->first();
        if (! $role) {
            return;
        }

        $ur = new UserRoleModel();
        $exists = $ur->where('user_id', $userId)->where('role_id', $role['id'])->first();
        if (! $exists) {
            $ur->insert(['user_id' => $userId, 'role_id' => $role['id']]);
        }
    }
}
