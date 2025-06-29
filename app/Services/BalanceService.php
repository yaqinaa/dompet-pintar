<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use App\Models\SavingGoal;
use Illuminate\Support\Facades\DB;

class BalanceService
{
    public function updateBalancesAfterTransaction(Transaction $transaction)
    {
        DB::transaction(function () use ($transaction) {
            $user = $transaction->user;
            $amount = $transaction->amount;

            switch ($transaction->type) {
                case 'income':
                    // Pemasukan: Tambah saldo utama
                    $user->balance += $amount;
                    break;

                case 'expense':
                    // Pengeluaran: Kurangi saldo utama
                    $user->balance -= $amount;
                    break;
                    
                case 'saving_deposit':
                    // Setor Tabungan: Kurangi saldo utama, tambah saldo di tujuan tabungan
                    $user->balance -= $amount;
                    if ($transaction->savingGoal) {
                        $transaction->savingGoal->saved_amount += $amount;
                        $transaction->savingGoal->save();
                    }
                    break;

                case 'saving_withdrawal':
                    // Tarik Tabungan: Tambah saldo utama, kurangi saldo di tujuan tabungan
                    $user->balance += $amount;
                    if ($transaction->savingGoal) {
                        $transaction->savingGoal->saved_amount -= $amount;
                        $transaction->savingGoal->save();
                    }
                    break;
            }

            // Simpan perubahan saldo user
            $user->save();
        });
    }
}