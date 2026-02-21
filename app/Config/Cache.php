<?php

declare(strict_types=1);

namespace Config;

use CodeIgniter\Cache\CacheInterface;
use CodeIgniter\Cache\Handlers\DummyHandler;
use CodeIgniter\Cache\Handlers\RedisHandler;
use CodeIgniter\Config\BaseConfig;

class Cache extends BaseConfig
{
    public string $handler = 'redis';
    public string $backupHandler = 'dummy';
    public string $prefix = 'pixelhavn_';
    public int $ttl = 60;
    public string $reservedCharacters = '{}()/\@:';

    public array $redis = [
        'host' => 'redis',
        'password' => null,
        'port' => 6379,
        'timeout' => 1,
        'async' => false,
        'persistent' => false,
        'database' => 0,
    ];

    public array $validHandlers = [
        'dummy' => DummyHandler::class,
        'redis' => RedisHandler::class,
    ];

    public $cacheQueryString = false;
    public array $cacheStatusCodes = [200];
}
