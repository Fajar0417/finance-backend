<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index()
    {
        return Transaction::with('wallet')->latest()->get();
    }

   public function store(Request $request)
{
    $request->validate([
        'type' => 'required',
        'amount' => 'required|numeric',
        'category' => 'required',
        'date' => 'required',
        'wallet_id' => 'required|exists:wallets,id',
    ]);

    $wallet = Wallet::find($request->wallet_id);

    if (!$wallet) {
        return response()->json(['error' => 'Wallet tidak ditemukan'], 404);
    }

    $transaction = Transaction::create($request->all());

    if ($request->type === 'income') {
        $wallet->balance += $request->amount;
    } else {
        $wallet->balance -= $request->amount;
    }

    $wallet->save();

    return response()->json($transaction);
}
  public function update(Request $request, $id)
{
    return DB::transaction(function () use ($request, $id) {

        $transaction = Transaction::findOrFail($id);

        $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:1',
            'wallet_id' => 'required|exists:wallets,id',
        ]);

        // =========================
        // 🔥 SIMPAN DATA LAMA (WAJIB)
        // =========================
        $oldType = $transaction->type;
        $oldAmount = $transaction->amount;
        $oldWalletId = $transaction->wallet_id;

        // 🔥 ambil wallet lama SEBELUM update
        $oldWallet = Wallet::findOrFail($oldWalletId);

        // =========================
        // 🔥 BALIKIN SALDO LAMA
        // =========================
        if ($oldType === 'income') {
            $oldWallet->balance -= $oldAmount;
        } else {
            $oldWallet->balance += $oldAmount;
        }
        $oldWallet->save();

        // =========================
        // 🔥 UPDATE TRANSAKSI
        // =========================
        $transaction->update([
            'type' => $request->type,
            'amount' => $request->amount,
            'category' => $request->category,
            'date' => $request->date,
            'description' => $request->description,
            'wallet_id' => $request->wallet_id,
        ]);

        // 🔥 ambil wallet baru SETELAH update
        $newWallet = Wallet::findOrFail($request->wallet_id);

        // =========================
        // 🔥 APPLY SALDO BARU
        // =========================
        if ($request->type === 'income') {
            $newWallet->balance += $request->amount;
        } else {
            $newWallet->balance -= $request->amount;
        }
        $newWallet->save();

        return response()->json($transaction);
    });
}

    public function destroy($id)
{
    $transaction = Transaction::findOrFail($id);
    $wallet = Wallet::findOrFail($transaction->wallet_id);

    // 🔥 BALIKIN SALDO
    if ($transaction->type === 'income') {
        $wallet->balance -= $transaction->amount;
    } else {
        $wallet->balance += $transaction->amount;
    }

    $wallet->save();

    $transaction->delete();

    return response()->json(['message' => 'Deleted']);
}
}
