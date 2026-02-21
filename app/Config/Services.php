<?php

declare(strict_types=1);

namespace Config;

use App\Services\AuthService;
use App\Services\JwtService;
use App\Services\RoleService;
use App\Services\WebAuthnService;
use CodeIgniter\Config\BaseService;

class Services extends BaseService
{
    public static function jwt(bool $getShared = true): JwtService
    {
        if ($getShared) {
            return static::getSharedInstance('jwt');
        }

        return new JwtService();
    }

    public static function auth(bool $getShared = true): AuthService
    {
        if ($getShared) {
            return static::getSharedInstance('auth');
        }

        return new AuthService();
    }

    public static function roles(bool $getShared = true): RoleService
    {
        if ($getShared) {
            return static::getSharedInstance('roles');
        }

        return new RoleService();
    }

    public static function webauthn(bool $getShared = true): WebAuthnService
    {
        if ($getShared) {
            return static::getSharedInstance('webauthn');
        }

        return new WebAuthnService();
    }
}
