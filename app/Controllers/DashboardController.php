<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\RoomModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $data = $this->viewData;
        $data['rooms'] = (new RoomModel())->getPublicRooms();

        return view('dashboard/index', $data);
    }
}
