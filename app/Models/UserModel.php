<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['username', 'email', 'password_hash', 'is_banned', 'must_change_password'];
    protected $useTimestamps = true;

    public function findByUsernameOrEmail(string $value): ?array
    {
        return $this->groupStart()
            ->where('username', $value)
            ->orWhere('email', $value)
            ->groupEnd()
            ->first();
    }
}
