<?php

declare(strict_types=1);

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Session\Handlers\BaseHandler;
use CodeIgniter\Session\Handlers\RedisHandler;

class Session extends BaseConfig
{
    public string $driver = RedisHandler::class;
    public string $cookieName = 'pixelhavn_session';
    public int $expiration = 7200;
    public string $savePath = 'tcp://redis:6379?database=1&timeout=2&prefix=pixelhavn:';
    public bool $matchIP = false;
    public int $timeToUpdate = 300;
    public bool $regenerateDestroy = true;
    public ?string $DBGroup = null;
    public int $lockRetryInterval = 100_000;
    public int $lockMaxRetries = 300;
}
