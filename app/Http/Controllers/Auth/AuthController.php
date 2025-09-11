<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function loginEndpoint(Request $request){
        return response()->json([
            'status' => 'success',
            'message' => 'Please login!',
            'data' => 'Welcome to login endpoint'
        ]);
    }

    public function login(Request $request){
        $request->validate([
            'name' => 'required|string|alpha_dash:ascii',
            'password' => 'required',
        ]);

        $user = User::where('name', $request->name)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'name' => ['The provided credentials are incorrect.'],
            ]);
        }

        return response()->json([
            'message' => 'Login successfully',
            'status' => 'success',
            'data' => $user->createToken($request->name)->plainTextToken
        ]);
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successfully',
            'status' => 'success',
        ]);
    }
}
