<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Purchase;
use App\Models\Transactions;
use App\Models\Wallet;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    /**
     * Create a new purchase.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $purchase = Purchase::create([
            'name' => $request->name,
        ]);

        return response()->json(['message' => 'Purchase created successfully', 'purchase' => $purchase], 201);
    }

    /**
     * Get all purchases.
     */
    public function index()
    {
        $purchases = Purchase::all();
        return response()->json(['message' => 'Purchases retrieved successfully', 'purchases' => $purchases], 200);
    }

    public function buy(Request $request, $id, $bill_id)
    {

        $purchase = Purchase::find($id);

        if (!$purchase) {
            return response()->json(['message' => 'Purchase not found'], 404);
        }

        $bill = Bill::find($bill_id);

        if (!$bill) {
            return response()->json(['message' => 'Bill not found'], 404);
        }

        $user = $request->user();
        $wallet = Wallet::where('user_id', $user->id)->first();

        if (!$wallet) {
            return response()->json(['message' => 'Wallet not found'], 404);
        }

        $lock = Cache::lock('user_purchase_' . $user->id, 10);

        if ($lock->get()) {
            try {
                if ($wallet->balance < $bill->amount) {
                    $transaction = new Transactions();
                    $transaction->user_id = $user->id;
                    $transaction->wallet_id = $wallet->id;
                    $transaction->purchase_id = $purchase->id;
                    $transaction->bill_id = $bill->id;
                    $transaction->amount = $bill->amount;
                    $transaction->status = 'failed';
                    $transaction->save();

                    return response()->json(['message' => 'Insufficient balance to buy the bill. Transaction failed!'], 400);
                }

                $wallet->balance -= $bill->amount;
                $wallet->save();

                $transaction = new Transactions();
                $transaction->user_id = $user->id;
                $transaction->wallet_id = $wallet->id;
                $transaction->purchase_id = $purchase->id;
                $transaction->bill_id = $bill->id;
                $transaction->amount = $bill->amount;
                $transaction->status = 'completed';
                $transaction->save();

                return response()->json(['message' => 'Bill purchased successfully'], 200);

            } finally {
                $lock->release();
            }
        } else {
            return response()->json(['message' => 'Transaction in progress. Please try again later.'], 429);
        }

    }



    /**
     * Get a single purchase by ID.
     */
    public function show($id)
    {
        $purchase = Purchase::find($id);

        if (!$purchase) {
            return response()->json(['message' => 'Purchase not found'], 404);
        }

        return response()->json(['message' => 'Purchase retrieved successfully', 'purchase' => $purchase], 200);
    }

    /**
     * Update an existing purchase.
     */
    public function update(Request $request, $id)
    {
        $purchase = Purchase::find($id);

        if (!$purchase) {
            return response()->json(['message' => 'Purchase not found'], 404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $purchase->update([
            'name' => $request->name,
        ]);

        return response()->json(['message' => 'Purchase updated successfully', 'purchase' => $purchase], 200);
    }

    /**
     * Delete a purchase.
     */
    public function destroy($id)
    {
        $purchase = Purchase::find($id);

        if (!$purchase) {
            return response()->json(['message' => 'Purchase not found'], 404);
        }

        $purchase->delete();

        return response()->json(['message' => 'Purchase deleted successfully'], 200);
    }
}
