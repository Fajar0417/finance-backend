<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    // ================= GET =================
    public function index(Request $request)
    {
        return Budget::where(
            'user_id',
            $request->user()->id
        )->latest()->get();
    }

    // ================= STORE =================
    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required',
            'amount' => 'required|numeric',
        ]);

        $budget = Budget::create([
            'user_id' => $request->user()->id,
            'category' => $request->category,
            'amount' => $request->amount,
        ]);

        return response()->json($budget);
    }

    // ================= DELETE =================
    public function destroy($id)
    {
        $budget = Budget::findOrFail($id);

        $budget->delete();

        return response()->json([
            'message' => 'Deleted',
        ]);
    }
}
