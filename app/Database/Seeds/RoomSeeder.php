<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run()
    {
        $exists = $this->db->table('rooms')->where('name', 'Lobby')->countAllResults();
        if ($exists > 0) {
            return;
        }

        $this->db->table('rooms')->insert([
            'owner_user_id' => null,
            'name' => 'Lobby',
            'description' => 'Main public room for all players.',
            'is_public' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
