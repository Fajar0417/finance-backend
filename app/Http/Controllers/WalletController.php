<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    // =========================
    // GET ALL WALLET USER LOGIN
    // =========================
    public function index(Request $request)
    {
        return Wallet::with('transactions')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();
    }

    // =========================
    // CREATE WALLET
    // =========================
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'balance' => 'required|numeric',
        ]);

        $wallet = Wallet::create([
            'user_id' => $request->user()->id,
            'name' => $request->name,
            'balance' => $request->balance,
            'bank_name' => $request->bank_name,
            'type' => $request->type,
            'account_number' => $request->account_number,
        ]);

        return response()->json($wallet);
    }

    // =========================
    // SHOW SINGLE WALLET
    // =========================
    public function show(Request $request, $id)
    {
        $wallet = Wallet::with('transactions')
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json($wallet);
    }

    // =========================
    // UPDATE WALLET
    // =========================
    public function update(Request $request, $id)
    {
        $wallet = Wallet::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $wallet->update($request->all());

        return response()->json($wallet);
    }

    // =========================
    // DELETE WALLET
    // =========================
    public function destroy(Request $request, $id)
    {
        $wallet = Wallet::where('user_id', $request->user()->id)
            ->findOrFail($id);

        $wallet->delete();

        return response()->json([
            'message' => 'Wallet deleted'
        ]);
    }
}