<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\SiteSettingModel;
use App\Models\UserModel;
use App\Models\WebauthnCredentialModel;

class AuthController extends BaseController
{
    public function loginForm()
    {
        return view('auth/login', $this->viewData);
    }

    public function registerForm()
    {
        return view('auth/register', $this->viewData);
    }

    public function register()
    {
        $rules = [
            'username' => 'required|min_length[3]|max_length[24]|alpha_numeric',
            'email' => 'required|valid_email',
            'password' => 'required|min_length[8]|max_length[128]',
        ];

        if (! $this->validateData($this->request->getPost(), $rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        try {
            service('auth')->register(
                (string) $this->request->getPost('username'),
                (string) $this->request->getPost('email'),
                (string) $this->request->getPost('password')
            );
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->to('/login')->with('success', 'Registration complete. You can now log in.');
    }

    public function login()
    {
        $settings = new SiteSettingModel();
        if (! $settings->isEnabled('auth_password_enabled')) {
            return redirect()->to('/auth/passkey-login')->with('error', 'Password login has been disabled.');
        }

        $rules = [
            'identity' => 'required|min_length[3]|max_length[120]',
            'password' => 'required|min_length[8]|max_length[128]',
        ];

        if (! $this->validateData($this->request->getPost(), $rules)) {
            return redirect()->back()->withInput()->with('error', 'Invalid credentials payload.');
        }

        $user = service('auth')->verifyPassword(
            (string) $this->request->getPost('identity'),
            (string) $this->request->getPost('password')
        );

        if (! $user) {
            return redirect()->back()->withInput()->with('error', 'Invalid credentials.');
        }

        $this->setSessionUser($user);

        return redirect()->to('/dashboard');
    }

    public function passkeyLoginForm()
    {
        return view('auth/passkey_login', $this->viewData);
    }

    public function passkeyAssertionOptions()
    {
        $username = strtolower(trim((string) $this->request->getPost('username')));
        $user = (new UserModel())->where('username', $username)->first();
        if (! $user) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'User not found']);
        }

        $credentials = (new WebauthnCredentialModel())->where('user_id', $user['id'])->findAll();
        if ([] === $credentials) {
            return $this->response->setStatusCode(422)->setJSON(['error' => 'No passkeys found for this user']);
        }

        session()->set('passkey_login_user_id', $user['id']);
        $options = service('webauthn')->assertionOptions($credentials);

        return $this->response->setJSON($options);
    }

    public function passkeyVerifyLogin()
    {
        $credential = service('webauthn')->verifyAssertion(
            (string) $this->request->getPost('clientDataJSON'),
            (string) $this->request->getPost('authenticatorData'),
            (string) $this->request->getPost('signature'),
            (string) $this->request->getPost('credentialId')
        );

        if (! $credential) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Passkey login failed']);
        }

        $user = (new UserModel())->find($credential['user_id']);
        if (! $user || (int) $user['is_banned'] === 1) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Account unavailable']);
        }

        $this->setSessionUser($user);

        return $this->response->setJSON(['ok' => true, 'redirect' => '/dashboard']);
    }

    public function changePasswordForm()
    {
        return view('auth/change_password', $this->viewData);
    }

    public function changePassword()
    {
        $rules = [
            'password' => 'required|min_length[8]|max_length[128]',
            'password_confirm' => 'required|matches[password]',
        ];
        if (! $this->validateData($this->request->getPost(), $rules)) {
            return redirect()->back()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $userId = $this->requireUserId();
        (new UserModel())->update($userId, [
            'password_hash' => password_hash((string) $this->request->getPost('password'), PASSWORD_ARGON2ID),
            'must_change_password' => 0,
        ]);

        $user = (new UserModel())->find($userId);
        $this->setSessionUser($user);

        return redirect()->to('/dashboard')->with('success', 'Password updated.');
    }

    public function logout()
    {
        session()->destroy();

        return redirect()->to('/login');
    }

    private function setSessionUser(array $user): void
    {
        $roles = service('roles')->getRolesForUser((int) $user['id']);
        session()->set('user', [
            'id' => (int) $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'must_change_password' => (int) $user['must_change_password'],
            'roles' => $roles,
        ]);
    }
}
