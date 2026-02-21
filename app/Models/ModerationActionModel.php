<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class ModerationActionModel extends Model
{
    protected $table = 'moderation_actions';
    protected $allowedFields = ['action_type', 'target_user_id', 'by_user_id', 'reason', 'meta_json'];
    protected $returnType = 'array';
    protected $useTimestamps = false;
}
