<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Purchase;
use Illuminate\Http\Request;

class BillController extends Controller
{
    /**
     * Create a new bill.
     */
    public function store(Request $request)
    {
        $request->validate([
            'purchase_id' => 'required|exists:purchases,id',
            'name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        $bill = Bill::create([
            'purchase_id' => $request->purchase_id,
            'name' => $request->name,
            'amount' => $request->amount,
        ]);

        return response()->json(['message' => 'Bill created successfully', 'bill' => $bill], 201);
    }

    /**
     * Get all bills.
     */
    public function index()
    {
        $bills = Bill::all();
        return response()->json(['message' => 'Bills retrieved successfully', 'bills' => $bills], 200);
    }

    /**
     * Get a single bill by ID.
     */
    public function show($id)
    {
        $bill = Bill::find($id);

        if (!$bill) {
            return response()->json(['message' => 'Bill not found'], 404);
        }

        return response()->json(['message' => 'Bill retrieved successfully', 'bill' => $bill], 200);
    }

    /**
     * Update an existing bill.
     */
    public function update(Request $request, $id)
    {
        $bill = Bill::find($id);

        if (!$bill) {
            return response()->json(['message' => 'Bill not found'], 404);
        }

        $request->validate([
            'name' => 'string|max:255',
            'amount' => 'numeric|min:0',
        ]);

        $bill->update([
            'name' => $request->name,
            'amount' => $request->amount,
        ]);

        return response()->json(['message' => 'Bill updated successfully', 'bill' => $bill], 200);
    }

    /**
     * Delete a bill.
     */
    public function destroy($id)
    {
        $bill = Bill::find($id);

        if (!$bill) {
            return response()->json(['message' => 'Bill not found'], 404);
        }

        $bill->delete();

        return response()->json(['message' => 'Bill deleted successfully'], 200);
    }
}
