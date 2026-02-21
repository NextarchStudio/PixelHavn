<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\SiteSettingModel;
use App\Models\WebauthnCredentialModel;
use lbuchs\WebAuthn\WebAuthn;

class WebAuthnService
{
    private function client(): WebAuthn
    {
        $appName = 'PixelHavn';
        $rpId = (string) env('webauthn.rpId', 'localhost');
        $origin = [(string) env('webauthn.origin', 'http://localhost:8080')];

        return new WebAuthn($appName, $rpId, $origin);
    }

    public function createOptions(array $user, array $excludeCredentials = []): array
    {
        if (! (new SiteSettingModel())->isEnabled('auth_passkey_enabled')) {
            throw new \RuntimeException('Passkeys are disabled by site policy.');
        }

        $wa = $this->client();
        $args = $wa->getCreateArgs(
            $user['id'],
            $user['username'],
            $user['username'],
            60,
            false,
            true,
            $excludeCredentials
        );

        session()->set('webauthn_register_challenge', $args->challenge);

        return json_decode(json_encode($args, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
    }

    public function verifyRegistration(string $clientDataJSON, string $attestationObject): array
    {
        $challenge = (string) session('webauthn_register_challenge');
        $wa = $this->client();

        $data = $wa->processCreate(
            $clientDataJSON,
            $attestationObject,
            $challenge,
            true,
            true,
            false
        );

        return [
            'credential_id' => base64_encode($data->credentialId),
            'public_key' => base64_encode($data->credentialPublicKey),
            'sign_count' => $data->signCount,
        ];
    }

    public function assertionOptions(array $credentials): array
    {
        $wa = $this->client();
        $allow = [];
        foreach ($credentials as $cred) {
            $allow[] = base64_decode($cred['credential_id'], true);
        }

        $args = $wa->getGetArgs($allow, 60, true);
        session()->set('webauthn_auth_challenge', $args->challenge);

        return json_decode(json_encode($args, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
    }

    public function verifyAssertion(string $clientDataJSON, string $authenticatorData, string $signature, string $credentialId): ?array
    {
        $credModel = new WebauthnCredentialModel();
        $cred = $credModel->where('credential_id', $credentialId)->first();
        if (! $cred) {
            return null;
        }

        $challenge = (string) session('webauthn_auth_challenge');
        $wa = $this->client();
        $newSignCount = $wa->processGet(
            base64_decode($clientDataJSON, true),
            base64_decode($authenticatorData, true),
            base64_decode($signature, true),
            base64_decode($cred['public_key'], true),
            $challenge,
            null,
            true,
            true
        );

        $credModel->update($cred['id'], ['sign_count' => $newSignCount]);

        return $cred;
    }
}
