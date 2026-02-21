<?php

declare(strict_types=1);

namespace Config;

use App\Filters\AuthFilter;
use App\Filters\ForcePasswordChangeFilter;
use App\Filters\RealtimeIngestFilter;
use App\Filters\RoleFilter;
use CodeIgniter\Config\Filters as BaseFilters;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;

class Filters extends BaseFilters
{
    public array $aliases = [
        'csrf' => CSRF::class,
        'toolbar' => DebugToolbar::class,
        'honeypot' => Honeypot::class,
        'auth' => AuthFilter::class,
        'role' => RoleFilter::class,
        'force-password-change' => ForcePasswordChangeFilter::class,
        'realtime-ingest' => RealtimeIngestFilter::class,
    ];

    public array $required = [
        'before' => [],
        'after' => [
            'toolbar',
        ],
    ];

    public array $globals = [
        'before' => [
            'csrf' => ['except' => ['api/realtime/*']],
        ],
        'after' => [],
    ];

    public array $methods = [];
    public array $filters = [
        'force-password-change' => ['before' => ['dashboard*', 'rooms*', 'inventory*', 'admin*']],
    ];
}
