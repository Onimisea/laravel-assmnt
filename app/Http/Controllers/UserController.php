<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    // Fetch all users
    public function allUsers(Request $request)
    {
        // You could paginate or apply additional filters
        return User::all();
    }

    // Fetch a single user by ID
    public function getUser(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        return response()->json($user);
    }

    // Update user's name
    public function updateName(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = $request->user(); // The authenticated user
        // dd($user);
        $user->name = $request->name;

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }


        $user->save();

        return response()->json(['message' => 'Name updated successfully', 'user' => $user]);
    }

    // Change user's PIN
    public function changePin(Request $request)
    {
        $request->validate([
            'pin' => 'required|string|digits:4',
            'pin_confirmation' => 'required|same:pin'
        ]);

        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($user->pin) {
            $message = 'PIN changed successfully';
        } else {
            $message = 'PIN set successfully';
        }

        // Update the PIN
        $user->pin = $request->pin;
        $user->save();

        return response()->json(['message' => 'PIN updated successfully', 'user' => $user->name]);
    }

    public function deleteUser(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Optional: Delete related data (e.g., Wallet, Transactions, etc.)
        // Assuming the user has a wallet and you want to delete it
        $wallet = $user->wallet;
        if ($wallet) {
            $wallet->delete();
        }

        // Delete the user
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}
