<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllocationCategory extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description'];

    public function expensesCategories()
    {
        return $this->hasMany(ExpenseCategory::class);
    }
}