<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class WebauthnCredentialModel extends Model
{
    protected $table = 'webauthn_credentials';
    protected $allowedFields = ['user_id', 'credential_id', 'public_key', 'sign_count', 'transports_json'];
    protected $returnType = 'array';
    protected $useTimestamps = false;

    public function getUserCredentials(int $userId): array
    {
        return $this->where('user_id', $userId)->findAll();
    }
}
