<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthApiController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            if (Auth::user()->status != 'active') {
                return response()->json(['message' => 'Your Account is not active yet. Please contact admin!'], 403);
            }

            $token = Auth::user()->createToken('authToken')->plainTextToken;
            return response()->json(['token' => $token], 200);
        }

        return response()->json(['message' => 'Invalid email or password!'], 401);
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        return response()->json(['message' => 'Successfully logged out'], 200);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|max:225',
            'password' => 'required|max:225',
            'email' => 'required|email|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid input! Please check back!', 'errors' => $validator->errors()], 422);
        }

        // Role_id is automatically set to 3
        $role_id = 3;
        $status = 'active';
        $hashedPassword = Hash::make($request->input('password'));

        $user = User::create([
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'password' => $hashedPassword,
            'role_id' => $role_id,
            'status' => $status,
        ]);

        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json(['message' => 'Register Success!', 'token' => $token], 201);
    }
}
