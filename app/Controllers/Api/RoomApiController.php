<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\InventoryModel;
use App\Models\RoomModel;
use App\Models\RoomPlacedItemModel;

class RoomApiController extends BaseController
{
    public function show(int $id)
    {
        $room = (new RoomModel())->find($id);
        if (! $room) {
            return $this->response->setStatusCode(404)->setJSON(['error' => 'Room not found']);
        }

        return $this->response->setJSON($room);
    }

    public function placedItems(int $id)
    {
        return $this->response->setJSON((new RoomPlacedItemModel())->forRoom($id));
    }

    public function placeItem(int $id)
    {
        $rules = [
            'item_id' => 'required|integer',
            'x' => 'required|integer|greater_than_equal_to[0]',
            'y' => 'required|integer|greater_than_equal_to[0]',
            'rotation' => 'permit_empty|integer',
            'state_json' => 'permit_empty|max_length[1024]',
        ];

        if (! $this->validateData($this->request->getPost(), $rules)) {
            return $this->response->setStatusCode(422)->setJSON(['error' => 'Invalid placement payload']);
        }

        $userId = $this->requireUserId();
        $inventory = new InventoryModel();
        $entry = $inventory->where('user_id', $userId)->where('item_id', (int) $this->request->getPost('item_id'))->first();
        if (! $entry || (int) $entry['quantity'] < 1) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Item not available in inventory']);
        }

        $placed = new RoomPlacedItemModel();
        $placedId = $placed->insert([
            'room_id' => $id,
            'user_id' => $userId,
            'item_id' => (int) $this->request->getPost('item_id'),
            'x' => (int) $this->request->getPost('x'),
            'y' => (int) $this->request->getPost('y'),
            'rotation' => (int) ($this->request->getPost('rotation') ?? 0),
            'state_json' => (string) ($this->request->getPost('state_json') ?? '{}'),
            'created_at' => date('Y-m-d H:i:s'),
        ], true);

        $inventory->update($entry['id'], ['quantity' => (int) $entry['quantity'] - 1]);

        return $this->response->setJSON(['ok' => true, 'placed_item_id' => (int) $placedId]);
    }
}
