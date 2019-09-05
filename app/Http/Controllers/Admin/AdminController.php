<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\User;
use Validator;
use Auth;

class AdminController extends Controller
{
    public function login(Request $request)
    {
        $email = $request->input('user_email');
        $password = $request->input('user_password');

        $check = User::where('user_email', '=', $email)->where('user_level', '=', 'admin')->first();

        if($check !== null) {
            if(Hash::check($password, $check->user_password)){
                $success['token'] =  $check->createToken($request->input('user_email'))->accessToken;
                return $this->sendResponseOkApi([
                    'token' => $success['token'],
                    'user' => Auth::user()
                ], 'You have successfully logged in');
            }else{
                return $this->sendResponseUnauthorizedApi('Password salah');
            }
        }else{
            return $this->sendResponseUnauthorizedApi('Email tidak terdaftar');
        }

        return $this->sendResponseBadRequestApi();
    }
}
