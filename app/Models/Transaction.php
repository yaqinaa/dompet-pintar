<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type', // 'income', 'expense', 'saving_deposit', 'saving_withdrawal'
        'description',
        'amount',
        'date',
        'expenses_category_id',
        'expense_allocation_id',
        'saving_goal_id',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function expenseCategory()
    {
        
        return $this->belongsTo(ExpenseCategory::class, 'expenses_category_id');
    }

    public function expenseAllocation()
    {
        return $this->belongsTo(ExpenseAllocation::class, 'expense_allocation_id');
    }

    public function savingGoal()
    {
        return $this->belongsTo(SavingGoal::class, 'saving_goal_id');
    }
}