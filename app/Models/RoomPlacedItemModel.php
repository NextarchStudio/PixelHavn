<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class RoomPlacedItemModel extends Model
{
    protected $table = 'room_placed_items';
    protected $allowedFields = ['room_id', 'user_id', 'item_id', 'x', 'y', 'rotation', 'state_json'];
    protected $returnType = 'array';
    protected $useTimestamps = false;

    public function forRoom(int $roomId): array
    {
        return $this->select('room_placed_items.*, items.name, items.item_key, users.username')
            ->join('items', 'items.id = room_placed_items.item_id')
            ->join('users', 'users.id = room_placed_items.user_id')
            ->where('room_placed_items.room_id', $roomId)
            ->orderBy('room_placed_items.id', 'ASC')
            ->findAll();
    }
}
