<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run()
    {
        $table = $this->db->table('site_settings');
        $table->ignore(true)->insert(['key' => 'auth_password_enabled', 'value' => '1']);
        $table->ignore(true)->insert(['key' => 'auth_passkey_enabled', 'value' => '1']);
    }
}
