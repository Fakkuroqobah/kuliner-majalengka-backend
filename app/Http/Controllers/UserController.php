<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\OauthAccessToken;
use App\User;
use Validator;

class UserController extends Controller
{
    public function login(Request $request){
        $email = $request->input('user_email');
        $password = $request->input('user_password');

        $check = User::where('user_email', $email)->first();

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

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_name' => 'required|max:20',
            'user_email' => 'required|email|unique:users,user_email',
            'user_password' => 'required',
            'password_confirmation' => 'required|same:user_password',
        ]);

        if ($validator->fails()) return $this->sendResponseUnproccessApi(['error' => $validator->errors()]);

        $user = User::create([
            'user_name' => $request->input('user_name'),
            'user_email' => $request->input('user_email'),
            'user_password' => Hash::make($request->input('user_password')),
            'user_level' => $request->input('user_level'),
            'user_level' => 'user',
        ]);

        if(!$user) return $this->sendResponseBadRequestApi();

        $success['token'] =  $user->createToken($request->input('user_name'))->accessToken;
        $success['name'] =  $user->user_name;

        return $this->sendResponseCreatedApi([
            'token' => $success['token']
        ], 'You have been registered');
    }

    public function logout()
    {
        if(Auth::check()) {
            $delete = Auth::user()->OauthAccessToken()->delete();

            if(!$delete) return $this->sendResponseBadRequestApi();

            return $this->sendResponseOkApi([], 'logout successfully');
        }
    }

    public function details()
    {
        return $this->sendResponseOkApi(['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_name' => 'required|max:20'
        ]);

        if ($validator->fails()) return $this->sendResponseUnproccessApi(['error' => $validator->errors()]);

        $user = User::findOrFail(Auth::user()->id_user);

        if(!$user) return $this->sendResponseNotFoundApi();

        $update = $user->update([
            'user_name' => $request->input('user_name')
        ]);

        if(!$update) return $this->sendResponseBadRequestApi();

        return $this->sendResponseUpdatedApi();
    }
}