<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    use HasFactory;
    protected $table = 'expenses_categories';
    protected $fillable = ['allocation_category_id', 'name', 'user_id'];

    public function allocationCategory()
    {
        return $this->belongsTo(AllocationCategory::class);
    }

    public function expensesAllocations()
    {
        return $this->hasMany(ExpenseAllocation::class);
    }
    
}