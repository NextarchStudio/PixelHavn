<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class InventoryModel extends Model
{
    protected $table = 'inventories';
    protected $allowedFields = ['user_id', 'item_id', 'quantity', 'unique_data_json'];
    protected $returnType = 'array';
    protected $useTimestamps = false;

    public function getByUser(int $userId): array
    {
        return $this->select('inventories.*, items.name, items.item_key, items.type')
            ->join('items', 'items.id = inventories.item_id')
            ->where('inventories.user_id', $userId)
            ->orderBy('inventories.id', 'ASC')
            ->findAll();
    }
}
