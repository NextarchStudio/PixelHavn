<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\ChatMessageModel;

class RealtimeController extends BaseController
{
    public function authToken()
    {
        $user = session('user');
        $roomId = (int) $this->request->getPost('room_id');

        if (! $user || $roomId < 1) {
            return $this->response->setStatusCode(422)->setJSON(['error' => 'Invalid request']);
        }

        $jwt = service('jwt')->issue([
            'sub' => (int) $user['id'],
            'username' => $user['username'],
            'roles' => $user['roles'],
            'room_id' => $roomId,
            'aud' => 'pixelhavn-realtime',
        ], 3600);

        return $this->response->setJSON(['token' => $jwt]);
    }

    public function ingestEvent()
    {
        $event = (string) $this->request->getPost('event');

        if ($event === 'chatMessage') {
            $rules = [
                'room_id' => 'required|integer',
                'user_id' => 'required|integer',
                'message' => 'required|min_length[1]|max_length[500]',
            ];
            if (! $this->validateData($this->request->getPost(), $rules)) {
                return $this->response->setStatusCode(422)->setJSON(['error' => 'Invalid payload']);
            }

            (new ChatMessageModel())->insert([
                'room_id' => (int) $this->request->getPost('room_id'),
                'user_id' => (int) $this->request->getPost('user_id'),
                'message' => trim((string) $this->request->getPost('message')),
                'created_at' => date('Y-m-d H:i:s'),
                'flagged' => preg_match('/(spam|hate|abuse)/i', (string) $this->request->getPost('message')) ? 1 : 0,
            ]);

            return $this->response->setJSON(['ok' => true]);
        }

        return $this->response->setStatusCode(422)->setJSON(['error' => 'Unknown event']);
    }
}
