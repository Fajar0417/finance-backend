<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    // =========================
    // GET TRANSACTIONS USER LOGIN
    // =========================
    public function index(Request $request)
    {
        return Transaction::with('wallet')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();
    }

    // =========================
    // STORE
    // =========================
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'amount' => 'required|numeric',
            'category' => 'required',
            'date' => 'required',
            'wallet_id' => 'required|exists:wallets,id',
        ]);

        $wallet = Wallet::where('user_id', $request->user()->id)
            ->findOrFail($request->wallet_id);

        $transaction = Transaction::create([
            'user_id' => $request->user()->id,
            'type' => $request->type,
            'amount' => $request->amount,
            'category' => $request->category,
            'date' => $request->date,
            'description' => $request->description,
            'wallet_id' => $request->wallet_id,
        ]);

        // UPDATE SALDO
        if ($request->type === 'income') {
            $wallet->balance += $request->amount;
        } else {
            $wallet->balance -= $request->amount;
        }

        $wallet->save();

        return response()->json($transaction);
    }

    // =========================
    // UPDATE
    // =========================
    public function update(Request $request, $id)
    {
        return DB::transaction(function () use ($request, $id) {

            $transaction = Transaction::where(
                'user_id',
                $request->user()->id
            )->findOrFail($id);

            $request->validate([
                'type' => 'required|in:income,expense',
                'amount' => 'required|numeric|min:1',
                'wallet_id' => 'required|exists:wallets,id',
            ]);

            // =========================
            // DATA LAMA
            // =========================
            $oldType = $transaction->type;
            $oldAmount = $transaction->amount;
            $oldWalletId = $transaction->wallet_id;

            $oldWallet = Wallet::findOrFail($oldWalletId);

            // =========================
            // BALIKIN SALDO LAMA
            // =========================
            if ($oldType === 'income') {
                $oldWallet->balance -= $oldAmount;
            } else {
                $oldWallet->balance += $oldAmount;
            }

            $oldWallet->save();

            // =========================
            // UPDATE TRANSACTION
            // =========================
            $transaction->update([
                'type' => $request->type,
                'amount' => $request->amount,
                'category' => $request->category,
                'date' => $request->date,
                'description' => $request->description,
                'wallet_id' => $request->wallet_id,
            ]);

            // =========================
            // APPLY SALDO BARU
            // =========================
            $newWallet = Wallet::findOrFail($request->wallet_id);

            if ($request->type === 'income') {
                $newWallet->balance += $request->amount;
            } else {
                $newWallet->balance -= $request->amount;
            }

            $newWallet->save();

            return response()->json($transaction);
        });
    }

    // =========================
    // DELETE
    // =========================
    public function destroy(Request $request, $id)
    {
        $transaction = Transaction::where(
            'user_id',
            $request->user()->id
        )->findOrFail($id);

        $wallet = Wallet::findOrFail($transaction->wallet_id);

        // BALIKIN SALDO
        if ($transaction->type === 'income') {
            $wallet->balance -= $transaction->amount;
        } else {
            $wallet->balance += $transaction->amount;
        }

        $wallet->save();

        $transaction->delete();

        return response()->json([
            'message' => 'Deleted'
        ]);
    }
}