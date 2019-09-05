<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Restaurant;
use Validator;
use Auth;

class AdminUserController extends Controller
{
    public function all()
    {
        $users = User::orderBy('created_at', 'DESC')->where('user_level', '=', 'user')->paginate(30);

        return $this->sendResponseOkApi($users);
    }

    public function details($id)
    {
        $users = User::with('restaurants')->orderBy('created_at', 'DESC')->where('users.id_user', '=', $id)->first();

        return $this->sendResponseOkApi($users);
    }

    public function total()
    {
        $users = User::count();

        return $this->sendResponseOkApi($users);
    }
}
