<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run()
    {
        $items = [
            ['item_key' => 'sofa_red', 'name' => 'Red Sofa', 'type' => 'furniture'],
            ['item_key' => 'lamp_blue', 'name' => 'Blue Lamp', 'type' => 'furniture'],
            ['item_key' => 'plant_tropical', 'name' => 'Tropical Plant', 'type' => 'furniture'],
            ['item_key' => 'table_oak', 'name' => 'Oak Table', 'type' => 'furniture'],
            ['item_key' => 'chair_black', 'name' => 'Black Chair', 'type' => 'furniture'],
            ['item_key' => 'poster_city', 'name' => 'City Poster', 'type' => 'wall'],
            ['item_key' => 'rug_modern', 'name' => 'Modern Rug', 'type' => 'floor'],
            ['item_key' => 'bed_simple', 'name' => 'Simple Bed', 'type' => 'furniture'],
            ['item_key' => 'desk_small', 'name' => 'Small Desk', 'type' => 'furniture'],
            ['item_key' => 'clock_wall', 'name' => 'Wall Clock', 'type' => 'wall'],
        ];

        foreach ($items as $item) {
            $this->db->table('items')->ignore(true)->insert([
                'item_key' => $item['item_key'],
                'name' => $item['name'],
                'type' => $item['type'],
                'meta_json' => json_encode(['rarity' => 'common'], JSON_THROW_ON_ERROR),
                'is_tradeable' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
