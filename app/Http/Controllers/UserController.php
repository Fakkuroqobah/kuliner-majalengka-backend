<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\OauthAccessToken;
use App\User;

class UserController extends Controller
{
    public function login(Request $request){
        $email = $request->input('user_email');
        $password = $request->input('user_password');

        $check = User::where('user_email', $email)->first();

        if($check !== null) {
            if(Hash::check($password, $check->user_password)){
                $success['token'] =  $check->createToken($request->input('user_email'))->accessToken;
                return response()->json([
                    'success' => "You have successfully logged in",
                    'token' => $success['token'],
                    'user' => Auth::user()
                ], 200);
            }else{
                return response()->json(['error'=>'Try again'], 401);
            }
        }else{
            return response()->json(['error'=>'Unauthorized'], 401);
        }

        return response()->json(['error'=>'wow'], 405);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_name' => 'required|max:20',
            'user_email' => 'required|email|unique:users,user_email',
            'user_password' => 'required',
            'c_password' => 'required|same:user_password',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $user = User::create([
            'user_name' => $request->input('user_name'),
            'user_email' => $request->input('user_email'),
            'user_password' => Hash::make($request->input('user_password')),
            'user_level' => $request->input('user_level'),
            'user_level' => 'user',
        ]);

        $success['token'] =  $user->createToken($request->input('user_name'))->accessToken;
        $success['name'] =  $user->user_name;

        return response()->json([
            'success' => "You have been registered",
            'token' => $success['token']
        ], 200);
    }

    public function logout()
    {
        if(Auth::check()) {
            Auth::user()->OauthAccessToken()->delete();

            return response()->json(['success' => 'logout successfully'], 200);
        }
    }

    public function details()
    {
        return response()->json(['user' => Auth::user()], 200);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_name' => 'required|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);            
        }

        $user = User::find(Auth::user()->id_user);
        $user->update([
            'user_name' => $request->input('user_name')
        ]);

        return response()->json([
            'success' => "User successfully updated",
        ], 200);
    }
}