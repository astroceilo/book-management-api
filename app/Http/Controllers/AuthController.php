<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\LoginAuthRequest;
use App\Http\Requests\RegisterAuthRequest;

class AuthController extends Controller
{
    public function register(RegisterAuthRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        // return response()->json([
        //     'message' => 'User berhasil diregister',
        //     'user' => $user,
        // ], 201);

        $user->token = $token; // Add token to user model for resource

        return (new UserResource($user))
            ->additional(['message' => 'User berhasil diregister'])
            ->response()
            ->setStatusCode(201);
    }
    
    public function login(LoginAuthRequest $request)
    {
        $credentials = $request->validated();

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Email atau Password salah'], 401);
        }

        $user  = Auth::user();
        $token = $user->createToken('api-token')->plainTextToken;

        // return response()->json([
        //     'message' => 'Login berhasil',
        //     'token' => $token,
        //     'user'  => $user,
        // ], 200);

        $user->token = $token; // Add token to user model for resource

        return (new UserResource($user))
            ->additional(['message' => 'Login berhasil'])
            ->response()
            ->setStatusCode(200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out'], 200);
    }
}
