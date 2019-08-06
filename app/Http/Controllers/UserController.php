<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\User;

class UserController extends Controller
{
    public function login(Request $request){
        $email = $request->input('email');
        $password = $request->input('password');

        $check = User::where('user_email', $email)->first();

        if($check !== null) {
            if(Hash::check($password, $check->user_password)){
                $success['token'] =  $check->createToken('nApp')->accessToken;
                return response()->json([
                    'success' => "You have successfully logged in",
                    'token' => $success['token']
                ], 200);
            }else{
                return response()->json(['error'=>'Try again'], 401);
            }
        }else{
            return response()->json(['error'=>'Unauthorized'], 401);
        }
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
            return response()->json(['error' => $validator->errors()], 401);            
        }

        $user = User::create([
            'user_name' => $request->input('user_name'),
            'user_email' => $request->input('user_email'),
            'user_password' => Hash::make($request->input('user_password')),
            'user_level' => $request->input('user_level'),
        ]);

        $success['token'] =  $user->createToken('nApp')->accessToken;
        $success['name'] =  $user->user_name;

        return response()->json([
            'success' => "You have been registered",
            'token' => $success['token']
        ], 200);
    }

    public function details()
    {
        $user = Auth::user();
        return response()->json($user, 200);
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