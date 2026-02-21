<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class SiteSettingModel extends Model
{
    protected $table = 'site_settings';
    protected $primaryKey = 'key';
    protected $returnType = 'array';
    protected $allowedFields = ['key', 'value'];
    protected $useAutoIncrement = false;

    public function getValue(string $key, ?string $default = null): ?string
    {
        $row = $this->find($key);

        return $row['value'] ?? $default;
    }

    public function isEnabled(string $key): bool
    {
        return $this->getValue($key, '0') === '1';
    }

    public function setValue(string $key, string $value): void
    {
        $this->save(['key' => $key, 'value' => $value]);
    }
}
