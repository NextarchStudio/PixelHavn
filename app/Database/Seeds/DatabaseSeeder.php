<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call('RoleSeeder');
        $this->call('SiteSettingSeeder');
        $this->call('RoomSeeder');
        $this->call('ItemSeeder');
        $this->call('AdminSeeder');
    }
}
