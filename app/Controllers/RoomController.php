<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\RoomModel;
use App\Models\RoomPlacedItemModel;

class RoomController extends BaseController
{
    public function index()
    {
        $data = $this->viewData;
        $data['rooms'] = (new RoomModel())->getPublicRooms();

        return view('rooms/index', $data);
    }

    public function view(int $id)
    {
        $room = (new RoomModel())->find($id);
        if (! $room) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Room not found.');
        }

        $data = $this->viewData;
        $data['room'] = $room;
        $data['placedItems'] = (new RoomPlacedItemModel())->forRoom($id);
        $data['realtimeUrl'] = (string) env('realtime.url', 'http://localhost:3001');

        return view('rooms/view', $data);
    }
}
