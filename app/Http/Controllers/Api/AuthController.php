<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Register a new user account.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        $existing = User::where('email', $validated['email'])->first();
        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Email address is already registered.'
            ], 422);
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
            'api_token' => Str::random(40),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully.',
            'data' => [
                'user' => $user,
                'api_token' => $user->api_token,
            ]
        ], 201);
    }

    /**
     * Authenticate user and issue API token.
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid login credentials.'
            ], 401);
        }

        $user->api_token = Str::random(40);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Authentication successful.',
            'data' => [
                'user' => $user,
                'api_token' => $user->api_token,
            ]
        ]);
    }

    /**
     * Get current authenticated user details.
     */
    public function me(Request $request)
    {
        $token = $request->bearerToken() ?? $request->header('X-API-TOKEN');
        $user = User::where('api_token', $token)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Revoke user authentication token.
     */
    public function logout(Request $request)
    {
        $token = $request->bearerToken() ?? $request->header('X-API-TOKEN');
        $user = User::where('api_token', $token)->first();

        if ($user) {
            $user->api_token = null;
            $user->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out.'
        ]);
    }
}
