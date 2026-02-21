<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\InventoryModel;
use App\Models\ItemModel;
use App\Models\RoleModel;
use App\Models\UserModel;
use App\Models\UserRoleModel;
use RuntimeException;

class AuthService
{
    public function register(string $username, string $email, string $password): int
    {
        $users = new UserModel();
        if ($users->findByUsernameOrEmail($username) || $users->findByUsernameOrEmail($email)) {
            throw new RuntimeException('Username or email already exists.');
        }

        $id = $users->insert([
            'username' => strtolower(trim($username)),
            'email' => strtolower(trim($email)),
            'password_hash' => password_hash($password, PASSWORD_ARGON2ID),
            'is_banned' => 0,
            'must_change_password' => 0,
        ], true);

        $this->assignDefaultRole((int) $id);
        $this->seedStarterInventory((int) $id);

        return (int) $id;
    }

    public function verifyPassword(string $identity, string $password): ?array
    {
        $user = (new UserModel())->findByUsernameOrEmail(strtolower(trim($identity)));
        if (! $user || $user['is_banned']) {
            return null;
        }

        if (! password_verify($password, (string) $user['password_hash'])) {
            return null;
        }

        return $user;
    }

    private function assignDefaultRole(int $userId): void
    {
        $role = (new RoleModel())->where('name', 'User')->first();
        if (! $role) {
            throw new RuntimeException('Default role not found.');
        }

        (new UserRoleModel())->insert(['user_id' => $userId, 'role_id' => $role['id']]);
    }

    private function seedStarterInventory(int $userId): void
    {
        $itemModel = new ItemModel();
        $inventoryModel = new InventoryModel();
        $items = $itemModel->whereIn('item_key', ['sofa_red', 'lamp_blue', 'plant_tropical'])->findAll();

        foreach ($items as $item) {
            $inventoryModel->insert([
                'user_id' => $userId,
                'item_id' => $item['id'],
                'quantity' => 1,
                'unique_data_json' => json_encode(['seeded' => true], JSON_THROW_ON_ERROR),
            ]);
        }
    }
}
