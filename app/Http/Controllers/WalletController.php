<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index()
    {
        return Wallet::with('transactions')->get();
    }

    public function store(Request $request)
    {
        return Wallet::create($request->all());
    }

    public function show($id)
{
    $wallet = Wallet::with('transactions')->findOrFail($id);
    return response()->json($wallet);
}
}
