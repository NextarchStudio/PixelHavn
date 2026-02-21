<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class RoomModel extends Model
{
    protected $table = 'rooms';
    protected $allowedFields = ['owner_user_id', 'name', 'description', 'is_public'];
    protected $returnType = 'array';
    protected $useTimestamps = false;

    public function getPublicRooms(): array
    {
        return $this->where('is_public', 1)->orderBy('id', 'ASC')->findAll();
    }
}
