<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{
    public function create($user_id)
    {
        $user = User::find($user_id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $accountNumber = $this->generateAccountNumber($user->phone_number);

        // Create wallet with the generated account number
        $wallet = Wallet::create([
            'user_id' => $user_id,
            'account_number' => $accountNumber,
        ]);

        return response()->json(['message' => 'Wallet created successfully', 'wallet' => $wallet], 201);
    }

    public function index()
    {
        $wallets = Wallet::all();
        return response()->json(['wallets' => $wallets], 200);
    }

    public function show(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $wallet = Wallet::where('user_id', $user->id)->first();

        if (!$wallet) {
            return response()->json(['message' => 'Wallet not found'], 404);
        }

        return response()->json(['wallet' => $wallet], 200);
    }


    public function fundWallet(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $wallet = Wallet::where('user_id', $user->id)->first();

        if (!$wallet) {
            return response()->json(['message' => 'Wallet not found'], 404);
        }

        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $wallet->balance += $request->amount;
        $wallet->save();

        return response()->json(['message' => 'Wallet funded successfully', 'wallet' => $wallet], 200);
    }

    public function withdrawFromWallet(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $wallet = Wallet::where('user_id', $user->id)->first();

        if (!$wallet) {
            return response()->json(['message' => 'Wallet not found'], 404);
        }

        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        if ($wallet->balance < $request->amount) {
            return response()->json(['message' => 'Insufficient balance'], 400);
        }

        $wallet->balance -= $request->amount;
        $wallet->save();

        return response()->json(['message' => 'Withdrawal successful', 'wallet' => $wallet], 200);
    }



    private static function generateAccountNumber(string $phoneNumber)
    {
        $digits = preg_replace('/\D/', '', $phoneNumber);
        $accountNumber = substr($digits, -10);

        while (Wallet::where('account_number', $accountNumber)->exists()) {
            // If it exists, regenerate a new account number
            $accountNumber = str_pad(mt_rand(0, 9999999999), 10, '0', STR_PAD_LEFT);
        }

        return $accountNumber;
    }
}
