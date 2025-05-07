<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Contractor;
use App\Models\Consultancy;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected static $staticUsers = [
        [
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => 'admin'
        ],
        [
            'name' => 'Official User',
            'email' => 'official@example.com',
            'password' => 'password',
            'role' => 'official'
        ],
        [
            'name' => 'Contractor User',
            'email' => 'contractor@example.com',
            'password' => 'password',
            'role' => 'contractor'
        ],
        [
            'name' => 'Public User',
            'email' => 'public@example.com',
            'password' => 'password',
            'role' => 'public'
        ],
    ];

    protected static $staticContractors = [
        [
            'name' => 'Sample Contractor',
            'email' => 'contractor@example.com',
            'phone' => '1234567890',
            'address' => '123 Contractor St'
        ],
    ];

    protected static $staticConsultancies = [
        [
            'name' => 'Sample Consultancy',
            'email' => 'consultancy@example.com',
            'phone' => '0987654321',
            'address' => '456 Consultancy Ave'
        ],
    ];

    public function __construct()
    {
        $this->createStaticUsersIfMissing();
        $this->createStaticContractorsIfMissing();
        $this->createStaticConsultanciesIfMissing();
    }

    protected function createStaticUsersIfMissing()
    {
        foreach (self::$staticUsers as $staticUser) {
            User::firstOrCreate(
                ['email' => $staticUser['email']],
                [
                    'name' => $staticUser['name'],
                    'password' => Hash::make($staticUser['password']),
                    'role' => $staticUser['role']
                ]
            );
        }
    }

    protected function createStaticContractorsIfMissing()
    {
        foreach (self::$staticContractors as $staticContractor) {
            Contractor::firstOrCreate(
                ['email' => $staticContractor['email']],
                [
                    'name' => $staticContractor['name'],
                    'phone' => $staticContractor['phone'],
                    'address' => $staticContractor['address']
                ]
            );
        }
    }

    protected function createStaticConsultanciesIfMissing()
    {
        foreach (self::$staticConsultancies as $staticConsultancy) {
            Consultancy::firstOrCreate(
                ['email' => $staticConsultancy['email']],
                [
                    'name' => $staticConsultancy['name'],
                    'phone' => $staticConsultancy['phone'],
                    'address' => $staticConsultancy['address']
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