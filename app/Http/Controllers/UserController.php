<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use App\User;
use Validator;

class UserController extends Controller
{
    private function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60,
            'user' => Auth::user()
        ]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_email' => 'required|email',
            'user_password' => 'required',
        ]);

        if ($validator->fails()) return $this->sendResponseUnproccessApi(['error' => $validator->errors()]);

        $email = $request->input('user_email');
        $password = $request->input('user_password');

        try {
            
            if (!$token = Auth::attempt(['user_email' => $email, 'password' => $password])) {
                return $this->sendResponseUnauthorizedApi('Email atau Password Salah');
            }

        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            
            return response()->json(['Token Expired'], 401);

        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            
            return response()->json(['Invalid Token'], 401);

        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            
            return response()->json(['Absent Token' => $e->getMessage()], 500);

        }
        
        return $this->respondWithToken($token);

        // $check = User::where('user_email', '=', $email)->first();

        // if($check !== null) {
        //     if(Hash::check($password, $check->user_password)){
        //         // $token =  $check->createToken($request->input('user_email'));
        //         // $strToken = $token->accessToken;

        //         // return $this->sendResponseOkApi([
        //         //     'token' => $strToken,
        //         //     'user' => $check
        //         // ], 'You have successfully logged in');

        //         $token = $this->jwt($check);
                
        //         return $this->respondWithToken($token);
        //     }else{
        //         return $this->sendResponseUnauthorizedApi('Email atau Password Salah');
        //     }
        // }else{
        //     return $this->sendResponseUnauthorizedApi('Email atau Password Salah');
        // }

        // return $this->sendResponseBadRequestApi();
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

        // $success['token'] =  $user->createToken($request->input('user_name'))->accessToken;
        // $success['name'] =  $user->user_name;

        return $this->sendResponseCreatedApi('You have been registered');
    }

    public function logout(Request $request)
    {
        if(Auth::check()) {
            // $delete = $request->user()->token()->revoke();
            // if(!$delete) return $this->sendResponseBadRequestApi();

            $user = Auth::logout();

            return $this->sendResponseOkApi([], 'logout successfully');
        }

        return $this->sendResponseUnauthorizedApi();
    }

    public function refresh()
    {
        try {
            return response()->json([
                'refresh_token' => $this->manager->refresh($this->jwt->getToken())->get(),
                'token_type' => 'bearer',
                'expires_in' => Auth::factory()->getTTL() * 60,
                'user' => Auth::user()
            ]);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json($e->getMessage(), 500);
        }

        // return response()->json([
        //     'refresh_token' => JWTAuth::refresh(JWTAuth::getToken()),
        //     'token_type' => 'bearer',
        //     'expires_in' => Auth::factory()->getTTL() * 60,
        //     'user' => Auth::user()
        // ]);
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

        // UPLOAD IMAGE
        if(empty($request->file('user_image'))) {
            $img = $user->user_image;
        }else{
            // Save new image
            $img = $request->file('user_image')->getClientOriginalExtension();
            $img = str_random(30) . '.' . $img;
            $path = 'images/avatar/';
            $request->file('user_image')->move($path, $img);

            // and delete old image
            $imgDB = explode('/', $user->user_image);
            $imgDB = end($imgDB);

            $path = base_path("public/images/avatar/$imgDB");

            if ($user->user_image !== 'avatar.png') {
                if(file_exists($path)) {
                    unlink($path);
                }
            }
        }

        $update = $user->update([
            'user_name' => $request->input('user_name'),
            'user_image' => $img
        ]);

        if(!$update) return $this->sendResponseBadRequestApi();

        return $this->sendResponseUpdatedApi();
    }
}