<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = ['Developer', 'Chief', 'Accounting', 'Office', 'Driver', 'User'];
        foreach ($roles as $role) {
            $this->db->table('roles')->ignore(true)->insert(['name' => $role]);
        }
    }
}
