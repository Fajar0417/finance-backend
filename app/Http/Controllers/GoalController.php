<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Wallet;
use App\Models\Transaction;

class GoalController extends Controller
{
     public function index()
    {
        return Goal::where(
            'user_id',
           Auth::id()
        )->latest()->get();
    }

    // ================= STORE =================
    public function store(Request $request)
    {
        $goal = Goal::create([
            'user_id' =>Auth::id(),

            'goal_name' =>
                $request->goal_name,

            'target_amount' =>
                $request->target_amount,

            'current_amount' =>
                $request->current_amount ?? 0,

            'deadline' =>
                $request->deadline,

            'status' => 'active',

            'icon' =>
                $request->icon,

            'image' =>
                $request->image,

            'color' =>
                $request->color,

            'description' =>
                $request->description,

            'product_link' =>
                $request->product_link,
        ]);

        return response()->json($goal);
    }

    // ================= DELETE =================
    public function destroy($id)
    {
        Goal::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Deleted'
        ]);
    }

  public function addSaving(
    Request $request,
    $id
)
{
    // ================= GOAL =================
    $goal = Goal::findOrFail($id);

    // ================= WALLET =================
    $wallet = Wallet::findOrFail(
        $request->wallet_id
    );

    // ================= AMOUNT =================
    $amount = (int) $request->amount;

    // ================= VALIDATION =================
    if ($amount <= 0) {
        return response()->json([
            'message' => 'Nominal tidak valid'
        ], 400);
    }

    // ================= CEK SALDO =================
    if ($wallet->balance < $amount) {
        return response()->json([
            'message' => 'Saldo tidak cukup'
        ], 400);
    }

    // ================= UPDATE GOAL =================
    $goal->current_amount += $amount;

    // ================= STATUS =================
    if (
        $goal->current_amount >=
        $goal->target_amount
    ) {
        $goal->status = 'completed';
    }

    $goal->save();

    // ================= UPDATE WALLET =================
    $wallet->balance -= $amount;

    $wallet->save();

    // ================= CREATE TRANSACTION =================
   Transaction::create([

    'user_id' => Auth::id(),

    'wallet_id' => $wallet->id,

    'goal_id' => $goal->id,

    'type' => 'saving',

    'amount' => $amount,

    'category' => 'Financial Goal',

    'description' =>
        $request->note
            ? $request->note
            : 'Nabung untuk ' .
                $goal->goal_name,

    'date' => now(),
]);

    // ================= RESPONSE =================
    return response()->json([
        'message' => 'Berhasil menabung',
        'goal' => $goal,
    ]);
}
public function history($id)
{
    $transactions = Transaction::with('wallet')
        ->where('goal_id', $id)
        ->where('type', 'saving')
        ->latest()
        ->get();

    return response()->json(
        $transactions
    );
}
public function show($id)
{
    return Goal::with([
        'transactions.wallet'
    ])
    ->findOrFail($id);
}
}
