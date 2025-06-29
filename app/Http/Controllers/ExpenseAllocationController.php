<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExpenseAllocation; // Menggunakan model ExpenseAllocation yang baru direvisi
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException; // Import ini
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ExpenseAllocationController extends Controller
{
    public function store(Request $request)
    {
        // Importante: Jangan lupa kembalikan logika validasi di sini!
        // Saya akan tambahkan kembali di contoh di bawah.

        // Fungsi pembantu untuk membersihkan format Rupiah
        function cleanRupiahFormat($amountString) {
            // Hapus "Rp", spasi non-breaking (&nbsp; atau \u00a0), dan tanda titik sebagai ribuan
            // Ganti koma sebagai desimal (jika ada) menjadi titik, lalu parse sebagai integer
            return (int) preg_replace('/[^0-9]/', '', $amountString);
        }

        // --- HAPUS PENGALIHAN KE VIEW DEBUG DAN KEMBALIKAN KODE ASLI ----
        // $data = $request->all();
        // return view('debug.store-test-success', compact('data'));
        // -----------------------------------------------------------------

        try {
            $request->validate([
                'income_allocation_id' => 'required|exists:income_allocations,id',
                'allocation_category_id' => 'required|exists:allocation_categories,id',
                'allocations' => 'nullable|array',
                'allocations.*.id' => 'nullable|exists:expenses_allocations,id', // <-- Ini sudah benar
                'allocations.*.name' => 'required|string|max:255',
                // allocated_amount akan divalidasi setelah dibersihkan
                'allocations.*.allocated_amount' => 'required|string', // Validasi sebagai string dulu
            ]);
        } catch (ValidationException $e) {
            Log::error("Validation failed in ExpenseAllocationController@store: " . $e->getMessage(), [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            return redirect()->back()->withErrors($e->errors())->withInput();
        }

        $incomeAllocationId = $request->input('income_allocation_id');
        $allocationCategoryId = $request->input('allocation_category_id');
        $allocationsData = $request->input('allocations', []);
         $userId = Auth::id();

        DB::beginTransaction();
        try {
            $submittedExpenseIds = [];

            foreach ($allocationsData as $data) {
                $id = $data['id'] ?? null;
                $name = $data['name'];
                $allocatedAmount = cleanRupiahFormat($data['allocated_amount']); // <-- PENTING: Bersihkan format Rupiah di sini

                // Setelah dibersihkan, lakukan validasi numerik untuk allocated_amount
                if (!is_numeric($allocatedAmount) || $allocatedAmount < 0) {
                     throw ValidationException::withMessages([
                         'allocations.' . ($id ?? 'new') . '.allocated_amount' => 'Jumlah alokasi harus berupa angka positif.'
                     ]);
                }


                if ($id) {
                    ExpenseAllocation::where('id', $id)
                                    ->update([
                                        'name' => $name,
                                        'allocated_amount' => $allocatedAmount, // Nilai sudah bersih
                                    ]);
                    $submittedExpenseIds[] = $id;
                } else {
                    $newExpense = ExpenseAllocation::create([
                         'user_id' => $userId,
                        'income_allocation_id' => $incomeAllocationId,
                        'allocation_category_id' => $allocationCategoryId,
                        'name' => $name,
                        'allocated_amount' => $allocatedAmount, // Nilai sudah bersih
                    ]);
                    $submittedExpenseIds[] = $newExpense->id;
                }
            }

            // Bagian delete ini sudah benar, asalkan nama kolom di DB adalah 'income_allocation_id' dan 'allocation_category_id'
            ExpenseAllocation::where('income_allocation_id', $incomeAllocationId)
                             ->where('allocation_category_id', $allocationCategoryId)
                             ->whereNotIn('id', $submittedExpenseIds)
                             ->delete();

            DB::commit();

            return redirect()->back()->with('success', 'Alokasi pengeluaran berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error saving expense allocations: " . $e->getMessage(), [
                'request_data' => $request->all(),
                'exception' => $e
            ]);
            return redirect()->back()->withErrors('Terjadi kesalahan saat menyimpan alokasi pengeluaran: ' . $e->getMessage())->withInput();
        }
    }
}