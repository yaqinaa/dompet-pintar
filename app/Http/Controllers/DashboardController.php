<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; // <-- TAMBAHKAN INI
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use App\Models\SavingGoal;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman utama dashboard dengan data yang sudah diproses.
     */
    public function index(Request $request) // <-- TAMBAHKAN Request $request
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }
        
        $now = Carbon::now();

        // 1. SUMMARY CARDS
        $totalSaldo = $user->balance;
        $totalPengeluaranBulanIni = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereMonth('date', $now->month)
            ->whereYear('date', $now->year)
            ->sum('amount');
        $totalTabungan = SavingGoal::where('user_id', $user->id)
            ->where('is_archived', 0)
            ->sum('saved_amount');

        // ====================================================================
        // PERBAIKAN UNTUK GRAFIK INTERAKTIF
        // ====================================================================
        // Ambil nilai 'days' dari request, dengan default 7 jika tidak ada.
        $days = $request->input('days', 7);

        // Panggil helper dengan variabel $days yang dinamis
        $chartData = $this->getDailyFinancialChartData($user->id, $days);

        // 3. TRANSAKSI TERAKHIR
        $recentTransactions = Transaction::where('user_id', $user->id)
            ->with('expenseCategory')
            ->latest('date')->take(5)->get();
            
        // 4. DATA UNTUK TRANSACTION STATISTICS
        $totalTransactionValue = Transaction::where('user_id', $user->id)
            ->whereMonth('date', $now->month)
            ->whereYear('date', $now->year)
            ->sum('amount');

        $transactionsByCategory = Transaction::where('transactions.user_id', $user->id)
            ->whereMonth('transactions.date', $now->month)
            ->whereYear('transactions.date', $now->year)
            ->leftJoin('expenses_categories', 'transactions.expenses_category_id', '=', 'expenses_categories.id')
            ->select(
                DB::raw("COALESCE(expenses_categories.name, 'Pemasukan') as kategori"),
                DB::raw('SUM(transactions.amount) as total')
            )
            ->groupBy('kategori')
            ->get();

        $allocationData = [];
        if ($totalTransactionValue > 0) {
            $allocationData = $transactionsByCategory->map(function ($item) use ($totalTransactionValue) {
                return [
                    'kategori' => $item->kategori,
                    'persentase' => round(($item->total / $totalTransactionValue) * 100, 2)
                ];
            })->toArray();
        }

        // 5. TRANSAKSI TABUNGAN
        $savingTransactions = Transaction::where('user_id', $user->id)
            ->whereIn('type', ['saving_deposit', 'saving_withdrawal'])
            ->with('savingGoal')
            ->latest('date')->take(5)->get();

        // Kirim semua data ke view
        return view('dashboard', compact( // Pastikan nama viewnya benar
            'totalSaldo',
            'totalPengeluaranBulanIni',
            'totalTabungan',
            'chartData',
            'recentTransactions',
            'allocationData',
            'savingTransactions'
        ));
    }

    /**
     * Helper function untuk mengambil data chart harian.
     * Tidak ada perubahan di sini, karena sudah menerima parameter $days.
     */
    private function getDailyFinancialChartData($userId, $days)
{
    $endDate = Carbon::now()->endOfDay();
    $startDate = Carbon::now()->subDays($days - 1)->startOfDay();
    
    // Fungsi helper untuk mengurangi duplikasi kode
    $queryByType = function ($type) use ($userId, $startDate, $endDate) {
        return Transaction::where('user_id', $userId)
            ->where('type', $type)
            ->whereBetween('date', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(date) as transaction_date'),
                DB::raw('SUM(amount) as total')
            )
            ->groupBy('transaction_date')
            ->orderBy('transaction_date', 'ASC')
            ->get()
            ->pluck('total', 'transaction_date');
    };

    // Panggil helper untuk setiap jenis transaksi
    $pemasukan = $queryByType('income');
    $pengeluaran = $queryByType('expense');
    $setorTabungan = $queryByType('saving_deposit');
    $tarikTabungan = $queryByType('saving_withdrawal');

    $labels = [];
    $pemasukanData = [];
    $pengeluaranData = [];
    $setorTabunganData = [];
    $tarikTabunganData = [];
    
    // Loop untuk mengisi semua data
    for ($i = 0; $i < $days; $i++) {
        $currentDate = $startDate->copy()->addDays($i);
        $dateKey = $currentDate->format('Y-m-d');
        
        $labels[] = $currentDate->format('M d');
        
        $pemasukanData[] = $pemasukan->get($dateKey, 0);
        $pengeluaranData[] = $pengeluaran->get($dateKey, 0);
        $setorTabunganData[] = $setorTabungan->get($dateKey, 0);
        $tarikTabunganData[] = $tarikTabungan->get($dateKey, 0);
    }

    // Kembalikan semua data yang dibutuhkan oleh chart
    return [
        'labels' => $labels,
        'pemasukan' => $pemasukanData,
        'pengeluaran' => $pengeluaranData,
        'setor_tabungan' => $setorTabunganData,
        'tarik_tabungan' => $tarikTabunganData,
    ];
}
}