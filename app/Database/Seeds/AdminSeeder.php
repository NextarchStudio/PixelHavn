<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $existing = $this->db->table('users')->where('username', 'admin')->get()->getRowArray();
        if ($existing) {
            $userId = (int) $existing['id'];
        } else {
            $this->db->table('users')->insert([
                'username' => 'admin',
                'email' => 'admin@pixelhavn.local',
                'password_hash' => password_hash('Admin123!', PASSWORD_ARGON2ID),
                'is_banned' => 0,
                'must_change_password' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $userId = (int) $this->db->insertID();
        }

        $developerRole = $this->db->table('roles')->where('name', 'Developer')->get()->getRowArray();
        if ($developerRole) {
            $this->db->table('user_roles')->ignore(true)->insert([
                'user_id' => $userId,
                'role_id' => $developerRole['id'],
            ]);
        }

        $itemRows = $this->db->table('items')->get()->getResultArray();
        foreach (array_slice($itemRows, 0, 3) as $item) {
            $this->db->table('inventories')->insert([
                'user_id' => $userId,
                'item_id' => $item['id'],
                'quantity' => 2,
                'unique_data_json' => json_encode(['seed' => 'admin'], JSON_THROW_ON_ERROR),
            ]);
        }
    }
}
