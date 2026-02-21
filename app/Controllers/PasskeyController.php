<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\WebauthnCredentialModel;

class PasskeyController extends BaseController
{
    public function index()
    {
        $credentials = (new WebauthnCredentialModel())->getUserCredentials($this->requireUserId());
        $data = $this->viewData;
        $data['credentials'] = $credentials;

        return view('auth/passkeys', $data);
    }

    public function registrationOptions()
    {
        $user = session('user');
        $credentials = (new WebauthnCredentialModel())->getUserCredentials((int) $user['id']);
        $exclude = array_map(static fn(array $cred): string => base64_decode($cred['credential_id'], true), $credentials);

        return $this->response->setJSON(service('webauthn')->createOptions($user, $exclude));
    }

    public function registerCredential()
    {
        $result = service('webauthn')->verifyRegistration(
            (string) $this->request->getPost('clientDataJSON'),
            (string) $this->request->getPost('attestationObject')
        );

        $transportsRaw = (string) ($this->request->getPost('transports') ?? '[]');
        $transports = json_decode($transportsRaw, true);
        if (! is_array($transports)) {
            $transports = [];
        }

        (new WebauthnCredentialModel())->insert([
            'user_id' => $this->requireUserId(),
            'credential_id' => $result['credential_id'],
            'public_key' => $result['public_key'],
            'sign_count' => (int) $result['sign_count'],
            'transports_json' => json_encode($transports, JSON_THROW_ON_ERROR),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['ok' => true]);
    }

    public function delete(int $id)
    {
        (new WebauthnCredentialModel())->where('id', $id)->where('user_id', $this->requireUserId())->delete();

        return redirect()->to('/passkeys')->with('success', 'Passkey removed.');
    }
}
