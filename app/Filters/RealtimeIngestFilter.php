<?php

declare(strict_types=1);

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RealtimeIngestFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $provided = $request->getHeaderLine('X-Realtime-Secret');
        $expected = (string) env('realtime.sharedSecret');

        if ($provided === '' || ! hash_equals($expected, $provided)) {
            return service('response')->setStatusCode(403)->setJSON(['error' => 'Forbidden']);
        }

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
