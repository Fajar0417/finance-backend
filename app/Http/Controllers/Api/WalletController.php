<?php
use App\Models\Wallet;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WalletController extends Controller
{
    public function index()
{
    return Wallet::with(['transactions' => function ($q) {
        $q->latest()->take(3); // ambil 3 transaksi terakhir
    }])->get();
}

    public function store(Request $request)
    {
        return Wallet::create($request->all());
    }

    public function show($id)
    {
        return Wallet::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $wallet = Wallet::findOrFail($id);
        $wallet->update($request->all());
        return $wallet;
    }

    public function destroy($id)
    {
        Wallet::destroy($id);
        return response()->json(['message' => 'Deleted']);
    }
}
