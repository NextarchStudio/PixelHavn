<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\InventoryModel;

class InventoryController extends BaseController
{
    public function index()
    {
        $data = $this->viewData;
        $data['inventory'] = (new InventoryModel())->getByUser($this->requireUserId());

        return view('inventory/index', $data);
    }

    public function place()
    {
        $rules = [
            'room_id' => 'required|integer',
            'item_id' => 'required|integer',
            'x' => 'required|integer',
            'y' => 'required|integer',
            'rotation' => 'permit_empty|integer',
        ];

        if (! $this->validateData($this->request->getPost(), $rules)) {
            return redirect()->back()->with('error', 'Invalid placement payload.');
        }

        return redirect()->to('/rooms/' . (int) $this->request->getPost('room_id'));
    }
}
