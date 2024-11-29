<?php


namespace App\Http\Controllers;

use App\Models\Transactions;
use Illuminate\Http\Request;

class TransactionsController extends Controller
{
    public function index()
    {
        $transactions = Transactions::all();
        return response()->json(['message' => 'Purchases retrieved successfully', "transactions" => $transactions, 200]);
    }

    public function show($id)
    {
        $transaction = Transactions::find($id);

        if (!$transaction) {
            return response()->json(['message' => 'Transaction not found'], 404);
        }

        return response()->json(['message' => 'Purchases retrieved successfully', "transaction" => $transaction, 200]);
    }

}
