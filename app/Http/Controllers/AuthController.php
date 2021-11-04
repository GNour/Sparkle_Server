<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!$token = Auth::attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->createNewToken($token);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|between:2,100',
            'first_name' => 'required|string|between:2,100',
            'last_name' => 'required|string|between:2,100',
            'gender' => 'required',
            'position' => 'required',
            'phone_number' => 'required|between:7,15',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
            'card_uid' => 'required',
            'role' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        if ($request->hasFile('profile_picture')) {

            $pic = $request->file('profile_picture');
            $name = $request->title . "_" . date('YmdHis') . "." . $pic->getClientOriginalExtension();
            $destinationPath = public_path('/images');
            $pic->move($destinationPath, $name);
        } else {
            $name = "default.png";
        }

        $user = User::create(array_merge(
            $validator->validated(),
            ['profile_picture' => $name],
            ['password' => bcrypt($request->password)]
        ));

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user,
        ], 201);

    }

    public function logout()
    {
        Auth::logout();

        return response()->json(['message' => 'User successfully signed out']);
    }

    public function refresh()
    {
        return $this->createNewToken(Auth::refresh());
    }

    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 24 * 60,
            'user' => auth()->user(),
        ]);
    }

    public function checkUser()
    {
        return response()->json([
            'user' => auth()->user(),
        ]);
    }
}
