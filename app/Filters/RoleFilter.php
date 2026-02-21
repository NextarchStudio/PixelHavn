<?php

declare(strict_types=1);

namespace App\Filters;

use App\Services\RoleService;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $user = session('user');
        if (! $user) {
            return redirect()->to('/login');
        }

        $allowed = $arguments ?? [];
        if ([] === $allowed) {
            return null;
        }

        if (! service('roles')->hasAnyRole((int) $user['id'], $allowed)) {
            return redirect()->to('/dashboard')->with('error', 'Insufficient permissions.');
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
