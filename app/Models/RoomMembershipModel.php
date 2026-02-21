<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class RoomMembershipModel extends Model
{
    protected $table = 'room_memberships';
    protected $allowedFields = ['room_id', 'user_id', 'role'];
    protected $returnType = 'array';
    protected $useTimestamps = false;
}
