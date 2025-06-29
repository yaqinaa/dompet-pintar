<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomeAllocation extends Model
{
    protected $table = 'income_allocations';

    protected $fillable = [
        'user_id',
        'dana_alokasi',
        'persen_primer',
        'persen_sekunder',
        'persen_tersier',
        'persen_tabungan',
        'tanggal',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
