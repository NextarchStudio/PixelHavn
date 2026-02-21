<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ItemModel;
use App\Models\RoleModel;
use App\Models\RoomModel;
use App\Models\SiteSettingModel;
use App\Models\UserModel;

class AdminController extends BaseController
{
    public function index()
    {
        $data = $this->viewData;
        $data['settings'] = (new SiteSettingModel())->findAll();

        return view('admin/index', $data);
    }

    public function updateSettings()
    {
        $settings = new SiteSettingModel();
        $settings->setValue('auth_password_enabled', $this->request->getPost('auth_password_enabled') ? '1' : '0');
        $settings->setValue('auth_passkey_enabled', $this->request->getPost('auth_passkey_enabled') ? '1' : '0');

        return redirect()->to('/admin')->with('success', 'Settings updated.');
    }

    public function users()
    {
        $data = $this->viewData;
        $data['users'] = (new UserModel())->findAll();
        $data['roles'] = (new RoleModel())->findAll();

        return view('admin/users', $data);
    }

    public function assignRole(int $id)
    {
        $roleName = (string) $this->request->getPost('role_name');
        service('roles')->assignRole($id, $roleName);

        return redirect()->to('/admin/users')->with('success', 'Role assigned.');
    }

    public function toggleBan(int $id)
    {
        $actor = session('user');
        if (in_array('Accounting', $actor['roles'], true) || in_array('Office', $actor['roles'], true)) {
            return redirect()->to('/admin/users')->with('error', 'Your role cannot ban users.');
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);
        if ($user) {
            $userModel->update($id, ['is_banned' => (int) ! $user['is_banned']]);
        }

        return redirect()->to('/admin/users')->with('success', 'User moderation state updated.');
    }

    public function rooms()
    {
        $data = $this->viewData;
        $data['rooms'] = (new RoomModel())->findAll();

        return view('admin/rooms', $data);
    }

    public function createRoom()
    {
        $rules = ['name' => 'required|min_length[2]|max_length[120]'];
        if (! $this->validateData($this->request->getPost(), $rules)) {
            return redirect()->back()->with('error', 'Invalid room payload.');
        }

        (new RoomModel())->insert([
            'owner_user_id' => null,
            'name' => (string) $this->request->getPost('name'),
            'description' => (string) $this->request->getPost('description'),
            'is_public' => (int) ($this->request->getPost('is_public') ? 1 : 0),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/admin/rooms')->with('success', 'Room created.');
    }

    public function items()
    {
        $data = $this->viewData;
        $data['items'] = (new ItemModel())->findAll();

        return view('admin/items', $data);
    }

    public function createItem()
    {
        $rules = [
            'item_key' => 'required|min_length[3]|max_length[64]|alpha_dash',
            'name' => 'required|min_length[2]|max_length[100]',
            'type' => 'required|min_length[2]|max_length[40]',
        ];

        if (! $this->validateData($this->request->getPost(), $rules)) {
            return redirect()->back()->with('error', 'Invalid item payload.');
        }

        (new ItemModel())->insert([
            'item_key' => (string) $this->request->getPost('item_key'),
            'name' => (string) $this->request->getPost('name'),
            'type' => (string) $this->request->getPost('type'),
            'meta_json' => (string) ($this->request->getPost('meta_json') ?? '{}'),
            'is_tradeable' => (int) ($this->request->getPost('is_tradeable') ? 1 : 0),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/admin/items')->with('success', 'Item created.');
    }
}
