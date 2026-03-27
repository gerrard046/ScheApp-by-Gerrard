<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    /**
     * Tampilkan halaman dompet
     */
    public function index()
    {
        $user = auth()->user();

        // Buat wallet jika belum ada
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0]
        );

        // Ambil semua transaksi terbaru
        $transactions = WalletTransaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Statistik
        $totalIncome  = WalletTransaction::where('user_id', $user->id)->where('type', 'income')->sum('amount');
        $totalExpense = WalletTransaction::where('user_id', $user->id)->where('type', 'expense')->sum('amount');

        // Ringkasan per kategori (pengeluaran)
        $expenseByCategory = WalletTransaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        // Data chart bulanan (6 bulan terakhir)
        $monthlyData = WalletTransaction::where('user_id', $user->id)
            ->selectRaw("strftime('%Y-%m', created_at) as month, type, SUM(amount) as total")
            ->whereRaw("created_at >= date('now', '-6 months')")
            ->groupByRaw("strftime('%Y-%m', created_at), type")
            ->orderBy('month')
            ->get();

        return view('wallet.index', compact(
            'wallet',
            'transactions',
            'totalIncome',
            'totalExpense',
            'expenseByCategory',
            'monthlyData'
        ));
    }

    /**
     * Tambah saldo (top up)
     */
    public function topup(Request $request)
    {
        $request->validate([
            'amount'      => 'required|numeric|min:1000',
            'description' => 'required|string|max:255',
        ]);

        $user   = auth()->user();
        $wallet = Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0]);

        $wallet->balance += $request->amount;
        $wallet->save();

        WalletTransaction::create([
            'user_id'     => $user->id,
            'type'        => 'income',
            'amount'      => $request->amount,
            'description' => $request->description,
            'category'    => 'Top Up',
        ]);

        return redirect()->route('wallet.index')->with('success', '💰 Saldo berhasil ditambahkan! +Rp ' . number_format($request->amount, 0, ',', '.'));
    }

    /**
     * Catat uang masuk
     */
    public function income(Request $request)
    {
        $request->validate([
            'amount'      => 'required|numeric|min:1',
            'description' => 'required|string|max:255',
            'category'    => 'required|string|max:100',
        ]);

        $user   = auth()->user();
        $wallet = Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0]);

        $wallet->balance += $request->amount;
        $wallet->save();

        WalletTransaction::create([
            'user_id'     => $user->id,
            'type'        => 'income',
            'amount'      => $request->amount,
            'description' => $request->description,
            'category'    => $request->category,
        ]);

        return redirect()->route('wallet.index')->with('success', '✅ Uang masuk berhasil dicatat! +Rp ' . number_format($request->amount, 0, ',', '.'));
    }

    /**
     * Catat uang keluar
     */
    public function expense(Request $request)
    {
        $request->validate([
            'amount'      => 'required|numeric|min:1',
            'description' => 'required|string|max:255',
            'category'    => 'required|string|max:100',
        ]);

        $user   = auth()->user();
        $wallet = Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0]);

        if ($wallet->balance < $request->amount) {
            return redirect()->back()->with('error', '❌ Saldo tidak mencukupi! Saldo saat ini: Rp ' . number_format($wallet->balance, 0, ',', '.'));
        }

        $wallet->balance -= $request->amount;
        $wallet->save();

        WalletTransaction::create([
            'user_id'     => $user->id,
            'type'        => 'expense',
            'amount'      => $request->amount,
            'description' => $request->description,
            'category'    => $request->category,
        ]);

        return redirect()->route('wallet.index')->with('success', '📝 Pengeluaran berhasil dicatat! -Rp ' . number_format($request->amount, 0, ',', '.'));
    }

    /**
     * Hapus transaksi
     */
    public function destroy($id)
    {
        $user        = auth()->user();
        $transaction = WalletTransaction::where('id', $id)->where('user_id', $user->id)->firstOrFail();

        // Kembalikan saldo
        $wallet = Wallet::firstOrCreate(['user_id' => $user->id], ['balance' => 0]);
        if ($transaction->type === 'income') {
            $wallet->balance = max(0, $wallet->balance - $transaction->amount);
        } else {
            $wallet->balance += $transaction->amount;
        }
        $wallet->save();

        $transaction->delete();

        return redirect()->route('wallet.index')->with('success', '🗑️ Transaksi berhasil dihapus dan saldo disesuaikan.');
    }
}
