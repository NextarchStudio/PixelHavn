<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\SiteSettingModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    protected $request;
    protected $helpers = ['url', 'form'];
    protected ?array $currentUser = null;
    protected array $viewData = [];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger): void
    {
        parent::initController($request, $response, $logger);

        if ($this->request instanceof CLIRequest) {
            return;
        }

        $this->currentUser = session('user');
        $this->viewData['appTitle'] = 'PixelHavn — by Nextarch Studio';
        $this->viewData['currentUser'] = $this->currentUser;
        try {
            $settings = new SiteSettingModel();
            $this->viewData['passwordLoginEnabled'] = $settings->isEnabled('auth_password_enabled');
            $this->viewData['passkeyEnabled'] = $settings->isEnabled('auth_passkey_enabled');
        } catch (\Throwable) {
            $this->viewData['passwordLoginEnabled'] = true;
            $this->viewData['passkeyEnabled'] = true;
        }
    }

    protected function requireUserId(): int
    {
        return (int) ($this->currentUser['id'] ?? 0);
    }
}
