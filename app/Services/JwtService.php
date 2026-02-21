<?php

declare(strict_types=1);

namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JwtService
{
    public function issue(array $claims, int $ttl = 3600): string
    {
        $now = time();
        $payload = array_merge($claims, [
            'iat' => $now,
            'exp' => $now + $ttl,
        ]);

        return JWT::encode($payload, (string) env('jwt.secret'), 'HS256');
    }

    public function decode(string $jwt): array
    {
        return (array) JWT::decode($jwt, new Key((string) env('jwt.secret'), 'HS256'));
    }
}
