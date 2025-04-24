<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Static user configurations
    protected static $staticUsers = [
        [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'plain_password' => 'password', // Raw password for initial setup
            'role' => 'admin'
        ],
        [
            'name' => 'Official User',
            'email' => 'official@example.com',
            'plain_password' => 'password',
            'role' => 'official'
        ],
        [
            'name' => 'Contractor User',
            'email' => 'contractor@example.com',
            'plain_password' => 'password',
            'role' => 'contractor'
        ],
        [
            'name' => 'Public User',
            'email' => 'public@example.com',
            'plain_password' => 'password',
            'role' => 'public'
        ],
    ];

    public function __construct()
    {
        $this->createStaticUsersIfMissing();
    }

    /**
     * Create static users if they don't exist in the database
     */
    protected function createStaticUsersIfMissing()
    {
        foreach (self::$staticUsers as $staticUser) {
            User::firstOrCreate(
                ['email' => $staticUser['email']],
                [
                    'name' => $staticUser['name'],
                    'password' => Hash::make($staticUser['plain_password']),
                    'role' => $staticUser['role']
                ]
            );
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
            'message' => 'Login successful'
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,official,contractor,public',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
            'message' => 'Registration successful'
        ], 201);
    }
}