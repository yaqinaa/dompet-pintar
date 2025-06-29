<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\SavingGoal;
use App\Models\GoalAllocation;
use App\Models\IncomeAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SavingGoalController extends Controller
{
    
    public function index()
    {
        $userId = auth()->id();

        // Ambil semua goals aktif user
        $activeGoals = SavingGoal::where('user_id', $userId)
            ->where('is_archived', false)
            ->get();

        return view('saving-goals.index', compact('activeGoals'));
    }


    public function archived()
    {
        $archivedGoals = SavingGoal::where('user_id', auth()->id())
                                ->where('is_archived', true)
                                ->get();

        return view('saving-goals.archived', compact('archivedGoals'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'goal_name' => 'required|string',
            'target_amount' => 'required|numeric',
            'deadline' => 'required|date',
        ]);

        $savingGoal = SavingGoal::create([
            'user_id' => Auth::id(), //Auth::id(),
            'goal_name' => $request->goal_name,
            'target_amount' => $request->target_amount,
            'deadline' => $request->deadline,
            'saved_amount' => 0,
        ]);

        return redirect()->route('saving-goals.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $goal = SavingGoal::findOrFail($id);
        return view('saving-goals.show', compact('goal'));
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
        $goal = SavingGoal::findOrFail($id);

        $request->validate([
            'goal_name' => 'required',
            'target_amount' => 'required|numeric|min:1',
            'deadline' => 'required|date',
        ]);

        $goal->update($request->only('goal_name', 'target_amount', 'saved_amount', 'deadline'));

        return redirect()->route('saving-goals.index')->with('success', 'Goal berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $goal = SavingGoal::where('user_id', auth()->id())->findOrFail($id);
        $goal->delete();

        return redirect()->route('saving-goals.index')->with('success', 'Goal berhasil dihapus.');
    }

    public function addSaved(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'goal_id' => 'required|exists:saving_goals,id',
            'source_type' => 'required|in:alokasi,tambahan',
        ]);

        $user = auth()->user();
        $goal = SavingGoal::where('id', $request->goal_id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $amount = $request->amount;

        if ($request->source_type === 'alokasi') {
            // Ambil nilai alokasi dari mana pun kamu simpan (misalnya $goal->alokasi_bulanan)
            $alokasi = $goal->monthly_allocation ?? 0;

        }

        // Kurangi saldo bulanan di monthly_balances
        $now = now();
        $monthlyBalance = MonthlyBalance::where('user_id', $user->id)
            ->whereYear('month', $now->year)
            ->whereMonth('month', $now->month)
            ->first();

        if ($monthlyBalance) {
            $monthlyBalance->remaining_balance -= $amount;
            $monthlyBalance->save();
        }
        Log::info('Nilai amount:', [$goal->monthly_allocation]);
        return back()->with('success', 'Tabungan berhasil ditambahkan.');
    }

    public function archive(SavingGoal $goal)
    {
        if ($goal->status !== 'tercapai') {
            return back()->with('error', 'Goal belum tercapai!');
        }

        $goal->is_archived = true;
        $goal->save();

        return back()->with('success', 'Goal berhasil dipindahkan ke riwayat!');
    }

}
