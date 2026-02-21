<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class ItemModel extends Model
{
    protected $table = 'items';
    protected $allowedFields = ['item_key', 'name', 'type', 'meta_json', 'is_tradeable'];
    protected $returnType = 'array';
    protected $useTimestamps = false;
}
