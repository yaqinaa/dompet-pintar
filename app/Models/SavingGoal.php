<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavingGoal extends Model
{
    protected $fillable = [
        'user_id',
        'goal_name',
        'target_amount',
        'saved_amount',
        'deadline',
        'status',
    ];

    // Relasi ke User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function goalAllocations()
    {
        return $this->hasMany(\App\Models\GoalAllocation::class, 'saving_goal_id');
    }

    // Kalkulasi tabungan per bulan/hari
    public function getAmountPerMonthAttribute()
    {
        $months = now()->diffInMonths($this->deadline);
        return $months > 0 ? $this->target_amount / $months : $this->target_amount;
    }

    public function getAmountPerDayAttribute()
    {
        $days = now()->diffInDays($this->deadline);
        return $days > 0 ? $this->target_amount / $days : $this->target_amount;
    }

    public function getAmountPerYearAttribute()
    {
        $years = now()->diffInYears($this->deadline);
        return $years > 0 ? $this->target_amount / $years : $this->target_amount;
    }

    public function getRemainingAmountAttribute()
    {
        return $this->target_amount - $this->saved_amount;
    }

    protected static function booted()
    {
        static::saving(function ($goal) {
            $goal->status = $goal->saved_amount >= $goal->target_amount
                ? 'tercapai'
                : 'belum tercapai';
        });
    }


}
