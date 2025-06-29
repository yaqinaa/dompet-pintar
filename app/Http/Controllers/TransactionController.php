<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\ExpenseCategory;
use App\Models\ExpenseAllocation;
use App\Models\SavingGoal;
use App\Services\BalanceService;
use Illuminate\Validation\Rule;

class TransactionController extends Controller
{
    protected $balanceService;

    // Suntikkan service melalui constructor
    public function __construct(BalanceService $balanceService)
    {
        $this->balanceService = $balanceService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        $transactions = Transaction::where('user_id', $user->id)
                                ->with([
                                    'expenseCategory',
                                    'expenseAllocation.allocationCategory', // Sesuaikan jika tidak ada relasi ini
                                    'savingGoal'
                                ])
                                ->latest()
                                ->get();

        $expenseCategories = ExpenseCategory::where('user_id', $user->id)->get();

        // INI KOREKSI UTAMA: Ambil semua alokasi milik user_id ini
        $expenseAllocations = ExpenseAllocation::where('user_id', $user->id)->get();

        // Di dalam method index()
        $savingGoals = SavingGoal::where('user_id', $user->id)
                                ->where('is_archived', 0) // <-- TAMBAHKAN BARIS INI
                                ->get();

        return view('transactions.index', compact(
            'transactions',
            'expenseCategories',
            'expenseAllocations',
            'savingGoals'
        ));
    }

    public function store(Request $request)
    {
        // 1. ATURAN VALIDASI
        $rules = [
            'type' => ['required', Rule::in(['income', 'expense', 'saving_deposit', 'saving_withdrawal'])],
            'amount' => 'required|numeric|min:0',
            'date' => 'required|date',
        ];

        // Validasi kondisional untuk KATEGORI (jika user membuat kategori baru)
        if ($request->input('expenses_category_id') === 'add_new_category') {
            $rules['new_category_name'] = 'required|string|max:255|unique:expenses_categories,name,NULL,id,user_id,' . auth()->id();
        } else if ($request->input('type') === 'expense') {
            $rules['expenses_category_id'] = 'required|exists:expenses_categories,id,user_id,' . auth()->id();
        } else {
            // Untuk income dan savings, kategori dari form bersifat opsional karena akan di-handle otomatis
            $rules['expenses_category_id'] = 'nullable|exists:expenses_categories,id,user_id,' . auth()->id();
        }

        // Validasi kondisional berdasarkan JENIS TRANSAKSI
        $type = $request->input('type');
        if ($type === 'expense') {
            $rules['use_allocation'] = 'nullable|string';
            $rules['expense_allocation_id'] = ['nullable', 'exists:expenses_allocations,id,user_id,' . auth()->id(), Rule::requiredIf($request->filled('use_allocation'))];
            $rules['description'] = ['string', 'max:255', Rule::requiredIf(!$request->filled('use_allocation'))];
        } elseif ($type === 'saving_deposit' || $type === 'saving_withdrawal') {
            // PERUBAHAN: Aturan menjadi lebih sederhana.
            // Karena form sekarang memaksa pemilihan dari dropdown, saving_goal_id menjadi wajib.
            $rules['use_saving_goal'] = 'required|string'; // Memastikan sinyal hidden input terkirim
            $rules['saving_goal_id'] = 'required|exists:saving_goals,id,user_id,' . auth()->id();
            // Tidak ada lagi validasi untuk 'description' di sini karena tidak diisi manual.
        } else { // Untuk 'income'
            $rules['description'] = 'required|string|max:255';
        }

        $validatedData = $request->validate($rules);

        // 2. PERSIAPAN DATA UNTUK DISIMPAN
        $dataToStore = [
            'user_id' => auth()->id(),
            'type' => $validatedData['type'],
            'amount' => $validatedData['amount'],
            'date' => $validatedData['date'],
            'expense_allocation_id' => null, // default null
            'saving_goal_id' => null,      // default null
        ];

        // 3. LOGIKA PENANGANAN KATEGORI
        $expenseCategoryId = null;
        if ($request->input('expenses_category_id') === 'add_new_category') {
            $newCategory = ExpenseCategory::create([
                'name' => $validatedData['new_category_name'],
                'user_id' => auth()->id(),
            ]);
            $expenseCategoryId = $newCategory->id;
        } elseif (in_array($validatedData['type'], ['saving_deposit', 'saving_withdrawal'])) {
            // Logika ini tetap sama: cari atau buat kategori "Tabungan" secara otomatis.
            $savingCategory = ExpenseCategory::firstOrCreate(
                ['name' => 'Tabungan', 'user_id' => auth()->id()],
                ['description' => 'Kategori untuk transaksi terkait tabungan.']
            );
            $expenseCategoryId = $savingCategory->id;
        } else {
            // Untuk income atau expense yang sudah memilih kategori
            $expenseCategoryId = $validatedData['expenses_category_id'] ?? null;
        }
        $dataToStore['expenses_category_id'] = $expenseCategoryId;


        // 4. LOGIKA PENANGANAN DESCRIPTION & FOREIGN KEY LAINNYA
        if ($validatedData['type'] === 'expense' && $request->filled('use_allocation')) {
            $dataToStore['expense_allocation_id'] = $validatedData['expense_allocation_id'];
            $dataToStore['description'] = ExpenseAllocation::find($validatedData['expense_allocation_id'])->name;
        } elseif (in_array($validatedData['type'], ['saving_deposit', 'saving_withdrawal'])) {
            // PERUBAHAN: Logika disederhanakan.
            // Kita tidak perlu lagi memeriksa 'use_saving_goal' karena validasi sudah memastikannya.
            $dataToStore['saving_goal_id'] = $validatedData['saving_goal_id'];
            $dataToStore['description'] = SavingGoal::find($validatedData['saving_goal_id'])->goal_name;
        } else {
            // Ini akan berjalan untuk 'income' atau 'expense' dengan deskripsi manual.
            $dataToStore['description'] = $validatedData['description'];
        }

        $transaction = Transaction::create($dataToStore); 

        // LANGKAH UTAMA: PANGGIL SERVICE UNTUK UPDATE SEMUA SALDO
        if ($transaction) { // Sekarang $transaction sudah terdefinisi
            $this->balanceService->updateBalancesAfterTransaction($transaction);
        }

        return redirect()->route('transactions.index')->with('success', 'Transaksi berhasil ditambahkan!');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
