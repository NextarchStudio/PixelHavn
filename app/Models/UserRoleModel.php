<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class UserRoleModel extends Model
{
    protected $table = 'user_roles';
    protected $allowedFields = ['user_id', 'role_id'];
    protected $returnType = 'array';
    protected $useTimestamps = false;
}
