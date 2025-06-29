<?php

namespace App\Http\Controllers;

use App\Models\IncomeAllocation;
use App\Models\SavingGoal;
use App\Models\GoalAllocation;
use Illuminate\Http\Request;
use Carbon\Carbon;

class GoalAllocationController extends Controller
{


    /**
     * Simpan pembagian alokasi tabungan ke goals.
     */
    public function store(Request $request) 
    {
        // --- KODE UNTUK DEBUGGING SEMENTARA ---
        // Bersihkan input seperti yang sudah kita diskusikan sebelumnya
        $cleanedAllocations = [];
        if ($request->has('allocations') && is_array($request->allocations)) {
            foreach ($request->allocations as $goalId => $amount) {
                $cleanAmount = preg_replace('/[^0-9-]/', '', $amount);
                $cleanedAllocations[$goalId] = (int) $cleanAmount;
            }
        }
        $request->merge(['allocations' => $cleanedAllocations]);

        $request->validate([
            'income_allocation_id' => 'required|exists:income_allocations,id',
            'allocations' => 'required|array',
            'allocations.*' => 'nullable|numeric|min:0',
        ]);

        $income = IncomeAllocation::findOrFail($request->income_allocation_id);

        if ($income->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access');
        }

        $totalAlokasi = array_sum($request->allocations);
        $maksimum = ($income->dana_alokasi * $income->persen_tabungan) / 100;

        if ($totalAlokasi > $maksimum) {
            return back()->withInput()->with('error', 'Total alokasi melebihi batas dana tabungan bulan ini.');
        }

        foreach ($request->allocations as $goalId => $amount) {
            if ($amount > 0) {
                $goal = SavingGoal::find($goalId);
                if (!$goal) continue;

                $remaining = $goal->target_amount - $goal->saved_amount;
                $monthsLeft = Carbon::now()->startOfMonth()->diffInMonths(
                    Carbon::parse($goal->deadline)->startOfMonth()
                );

                $perMonthNeed = $monthsLeft > 0 ? ceil($remaining / $monthsLeft) : $remaining;

                if ($amount < $perMonthNeed) {
                    return back()->withInput()->withErrors([
                        "allocations.{$goalId}" => "Alokasi untuk \"{$goal->goal_name}\" kurang dari kebutuhan per bulan (Rp " . number_format($perMonthNeed, 0, ',', '.') . ")."
                    ]);
                }
            }
        }

        foreach ($request->allocations as $goalId => $amount) {
            if ($amount > 0) {
                $existing = GoalAllocation::where('income_allocation_id', $income->id)
                    ->where('saving_goal_id', $goalId)
                    ->first();

                if ($existing) {
                    $existing->update(['amount' => $amount]);
                } else {
                    GoalAllocation::create([
                        'income_allocation_id' => $income->id,
                        'saving_goal_id' => $goalId,
                        'amount' => $amount,
                    ]);
                }
            }
        }

        return redirect()->route('income-allocations.index')
            ->with('success', 'Alokasi ke goals berhasil disimpan.');
       
    
    }

}
