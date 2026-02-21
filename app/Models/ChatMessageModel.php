<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class ChatMessageModel extends Model
{
    protected $table = 'chat_messages';
    protected $allowedFields = ['room_id', 'user_id', 'message', 'flagged'];
    protected $returnType = 'array';
    protected $useTimestamps = false;
}
