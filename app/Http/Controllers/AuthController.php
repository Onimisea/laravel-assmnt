<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // Registration method
    public function register(Request $request)
    {
        // Validate request
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|min:11|max:11|unique:users|regex:/^\+?[0-9]\d{1,11}$/',
            'password' => [
                'required',
                'string',
                'min:5',
                'confirmed'
            ],
        ]);

        // Create a new user
        $user = User::create([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password), // Hash the password
        ]);

        // Generate a personal access token for the user
        $token = $user->createToken('BillPaymentApi')->plainTextToken;

        $walletController = new WalletController();
        $walletController->create($user->id);

        // Return response
        return response()->json([
            'message' => 'User registered successfully',
            'token' => $token,
        ]);
    }

    // Login method
    public function login(Request $request)
    {
        // Validate request
        $request->validate([
            'phone_number' => 'required|string',
            'password' => 'required|string',
        ]);

        // Check if the user exists
        $user = User::where('phone_number', $request->phone_number)->first();

        // If user not found or password mismatch
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Generate a token for the user
        $token = $user->createToken('YourAppName')->plainTextToken;

        // Return response
        return response()->json([
            'message' => 'User logged in successfully',
            'token' => $token,
        ]);
    }

    // Logout method
    public function logout(Request $request)
    {
        // Revoke the current user's token
        $request->user()->currentAccessToken()->delete();

        // Return response
        return response()->json(['message' => 'User logged out successfully']);
    }
}
