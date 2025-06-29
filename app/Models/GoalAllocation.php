<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoalAllocation extends Model
{
    protected $fillable = [
        'user_id',
        'income_allocation_id',
        'saving_goal_id',
        'amount',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function savingGoal()
    {
        return $this->belongsTo(SavingGoal::class);
    }

    public function incomeAllocation()
    {
        return $this->belongsTo(IncomeAllocation::class);
    }
}
