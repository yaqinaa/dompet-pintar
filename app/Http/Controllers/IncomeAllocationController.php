<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\IncomeAllocation;
use App\Models\SavingGoal;
use App\Models\GoalAllocation;
use App\Models\AllocationCategory;
use App\Models\ExpenseAllocation;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class IncomeAllocationController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        $filterDate = $request->input('tanggal', Carbon::now()->format('Y-m'));
        $selectedMonth = Carbon::createFromFormat('Y-m', $filterDate)->startOfMonth();
        $now = Carbon::now()->startOfMonth();

        $canAddData = $selectedMonth->greaterThanOrEqualTo($now);

        $allocation = IncomeAllocation::where('user_id', $userId)
            ->whereYear('tanggal', $selectedMonth->year)
            ->whereMonth('tanggal', $selectedMonth->month)
            ->first();

        $previousAllocations = IncomeAllocation::where('user_id', $userId)
            ->where('tanggal', '<', $selectedMonth)
            ->orderByDesc('tanggal')
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->tanggal)->format('Y-m');
            });

        $goals = SavingGoal::where('user_id', $userId)
            ->whereColumn('saved_amount', '<', 'target_amount')
            ->get();

        $tabunganTotal = 0;
        $existingAllocations = []; // Untuk GoalAllocation
        $totalIncomeAllocated = 0;
        $totalAllocatedAmount = 0;
        $remainingAllocationAmount = 0;

        // --- BARU: Inisialisasi untuk ExpenseAllocation (Data mentah) ---
        $allExistingExpenseAllocations = collect(); // Menggunakan koleksi untuk kemudahan
        // ---------------------------------------------------------------

        $allocationCategories = AllocationCategory::all()->keyBy('name');

        $dataKategori = [];

        if ($allocation) {
            $totalIncomeAllocated = $allocation->dana_alokasi;
            $tabunganTotal = ($totalIncomeAllocated * $allocation->persen_tabungan) / 100;

             // Hitung total dari ExpenseAllocation untuk income_allocation_id saat ini
            $totalExpensesAllocated = ExpenseAllocation::where('income_allocation_id', $allocation->id)->sum('allocated_amount');

            // Hitung total dari GoalAllocation untuk income_allocation_id saat ini
            $totalGoalsAllocated = GoalAllocation::where('income_allocation_id', $allocation->id)->sum('amount');

            // Total dana yang SUDAH dialokasikan (expense + goal)
            $totalAllocatedAmount = $totalExpensesAllocated + $totalGoalsAllocated;

            // Sisa dana yang BELUM dialokasikan
            $remainingAllocationAmount = $totalIncomeAllocated - $totalAllocatedAmount;

            // Logika GoalAllocation (Tidak Berubah)
            $existingAllocations = GoalAllocation::where('income_allocation_id', $allocation->id)
                ->pluck('amount', 'saving_goal_id')
                ->toArray();

            // --- BARU: Ambil semua ExpenseAllocation untuk income_allocation_id ini ---
            // Kita tidak perlu mengelompokkan di controller lagi,
            // karena Blade akan memfilter berdasarkan kategori yang diklik.
            $allExistingExpenseAllocations = ExpenseAllocation::where('income_allocation_id', $allocation->id)->get();
            // -------------------------------------------------------------------------

            $dataKategori = [
                'Primer' => [
                    'persen' => $allocation->persen_primer,
                    'nominal' => round($totalIncomeAllocated * $allocation->persen_primer / 100),
                    'allocation_category_id' => $allocationCategories['Primer']->id ?? null,
                ],
                'Sekunder' => [
                    'persen' => $allocation->persen_sekunder,
                    'nominal' => round($totalIncomeAllocated * $allocation->persen_sekunder / 100),
                    'allocation_category_id' => $allocationCategories['Sekunder']->id ?? null,
                ],
                'Tersier' => [
                    'persen' => $allocation->persen_tersier,
                    'nominal' => round($totalIncomeAllocated * $allocation->persen_tersier / 100),
                    'allocation_category_id' => $allocationCategories['Tersier']->id ?? null,
                ],
                'Tabungan' => [
                    'persen' => $allocation->persen_tabungan,
                    'nominal' => round($totalIncomeAllocated * $allocation->persen_tabungan / 100),
                    'allocation_category_id' => $allocationCategories['Tabungan']->id ?? null,
                ],
            ];
        } else {
            
            $dataKategori = [
                'Primer' => [
                    'persen' => 0, 'nominal' => 0, 'allocation_category_id' => $allocationCategories['Primer']->id ?? null,
                ],
                'Sekunder' => [
                    'persen' => 0, 'nominal' => 0, 'allocation_category_id' => $allocationCategories['Sekunder']->id ?? null,
                ],
                'Tersier' => [
                    'persen' => 0, 'nominal' => 0, 'allocation_category_id' => $allocationCategories['Tersier']->id ?? null,
                ],
                'Tabungan' => [
                    'persen' => 0, 'nominal' => 0, 'allocation_category_id' => $allocationCategories['Tabungan']->id ?? null,
                ],
            ];

            return view('income-allocations.index', compact(
                'allocation',
                'filterDate',
                'previousAllocations',
                'canAddData'                
            ))->with('message', 'Data alokasi untuk bulan ini belum tersedia.');

        }
        

        

        return view('income-allocations.index', compact(
            'allocation',
            'dataKategori',
            'totalIncomeAllocated',
            'filterDate',
            'previousAllocations',
            'canAddData',
            'goals',
            'tabunganTotal',
            'existingAllocations',
            'allExistingExpenseAllocations',
            'totalAllocatedAmount',        // <-- BARU: Total dana yang sudah dialokasikan
            'remainingAllocationAmount'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'dana_alokasi' => 'required|integer|min:0',
            'persen_primer' => 'required|integer|min:0|max:100',
            'persen_sekunder' => 'required|integer|min:0|max:100',
            'persen_tersier' => 'required|integer|min:0|max:100',
            'persen_tabungan' => 'required|integer|min:0|max:100',
            'tanggal' => 'required|date_format:Y-m',
        ]);

        // Simpan ke database
        IncomeAllocation::create([
            'user_id' => auth()->id(),
            'dana_alokasi' => $validated['dana_alokasi'],
            'persen_primer' => $validated['persen_primer'],
            'persen_sekunder' => $validated['persen_sekunder'],
            'persen_tersier' => $validated['persen_tersier'],
            'persen_tabungan' => $validated['persen_tabungan'],
            'tanggal' => $validated['tanggal'] . '-01', // format lengkap untuk kolom date
        ]);

        return redirect()->route('income-allocations.index')->with('message', 'Data berhasil ditambahkan.');
    }

    public function destroy(string $id)
    {
        $goal = IncomeAllocation::where('user_id', auth()->id())->findOrFail($id);
        $goal->delete();

        return redirect()->route('income-allocations.index')->with('success', 'Allokasi berhasil dihapus.');
    }

    public function update(Request $request, $id)
    {
        // Validasi input
        $validatedData = $request->validate([
            'tanggal' => 'required|date',
            'dana_alokasi' => 'required|numeric',
            'persen_primer' => 'required|numeric',
            'persen_sekunder' => 'required|numeric',
            'persen_tersier' => 'required|numeric',
            'persen_tabungan' => 'required|numeric',
        ]);

        // Cari alokasi yang akan diupdate
        $allocation = IncomeAllocation::findOrFail($id);

        // Update data alokasi dengan input yang diterima
        $allocation->tanggal = $validatedData['tanggal']. '-01';
        $allocation->dana_alokasi = $validatedData['dana_alokasi'];
        $allocation->persen_primer = $validatedData['persen_primer'];
        $allocation->persen_sekunder = $validatedData['persen_sekunder'];
        $allocation->persen_tersier = $validatedData['persen_tersier'];
        $allocation->persen_tabungan = $validatedData['persen_tabungan'];

        // Simpan perubahan
        $allocation->save();

        // Redirect atau beri feedback sukses
        return redirect()->route('income-allocations.index') // Bisa disesuaikan dengan route yang ingin dituju setelah update
                        ->with('success', 'Alokasi dana berhasil diperbarui.');
    }


}
