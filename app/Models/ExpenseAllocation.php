<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;


use Illuminate\Database\Eloquent\Model;

class ExpenseAllocation extends Model
{
    use HasFactory;
    protected $table = 'expenses_allocations';
    protected $fillable = [
        'user_id',
        'income_allocation_id',
         'allocation_category_id', // Tambahkan ini agar bisa memfilter berdasarkan kategori induk
        'name', // Nama alokasi spesifik seperti 'Makan Sehari-hari'
        'allocated_amount',
    ];

    public function incomeAllocation()
    {
        return $this->belongsTo(IncomeAllocation::class);
    }

    public function expensesCategory()
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'expense_allocation_id');
    }
     public function allocationCategory()
    {
        return $this->belongsTo(AllocationCategory::class);
    }
}