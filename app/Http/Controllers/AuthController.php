<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales no son validas.'],
            ]);
        }

        if ($request->hasSession()) {
            auth()->guard('web')->login($user);
            $request->session()->regenerate();
        }

        $token = $user->createToken('vue-admin')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
        if ($bearerToken = $request->bearerToken()) {
            PersonalAccessToken::findToken($bearerToken)?->delete();
        }

        $accessToken = $request->user()?->currentAccessToken();

        if ($accessToken instanceof PersonalAccessToken && $accessToken->exists) {
            $accessToken->delete();
        }

        if ($request->hasSession()) {
            auth()->guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return response()->json([
            'mensaje' => 'Sesion cerrada',
        ]);
    }
}
