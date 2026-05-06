<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiTrait;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ApiTrait;

    public function allUsers()
    {
        $users = User::select('id', 'name', 'slack_username', 'email', 'phone', 'is_super_admin', 'google_id', 'department_id', 'title_id')
            ->get();

        if ($users->isEmpty()) {
            return $this->failure('No user found.');
        }

        return $this->success('Users fetched successfully', $users);
    }

    public function getUser(Request $request)
    {
        $userId = $request->userId;
        if (!$userId) {
            return $this->failure('No user found.');
        }

        $user = User::select('id', 'name', 'slack_username', 'email', 'phone', 'is_super_admin', 'google_id', 'department_id', 'title_id')
            ->where('id', $userId)
            ->get();

        return $this->success('User data fetched successfully', $user);
    }

    public function editUser(Request $request, $userId)
    {
        $request->validate([
            'department_id'     => 'required',
            'title_id'          => 'required',
            'phone'          => 'required',
            'slack_username' => 'required',
        ]);

        $user = User::findOrFail($userId);
        if (!$user) {
            return $this->failure('User not found');
        }

        $user->department_id = $request->department_id;
        $user->title_id = $request->title_id;
        $user->phone = $request->phone;
        $user->slack_username = $request->slack_username;
        $user->save();

        return $this->success('User updated successfully', $user);
    }
}
