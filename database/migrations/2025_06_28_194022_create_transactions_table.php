<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('type', ['income', 'expense', 'saving_deposit', 'saving_withdrawal']); // Tipe transaksi
            $table->string('description')->nullable();
            $table->bigInteger('amount');
            $table->date('date');

            // Foreign key untuk KATEGORI PENGELUARAN UMUM
            // Ini WAJIB diisi untuk type='expense'. Dari tabel `expenses_categories`.
            $table->unsignedBigInteger('expenses_category_id')->nullable();
            
            // Foreign key OPSIONAL untuk ALOKASI SPESIFIK (dari modal alokasi)
            // Ini akan diisi HANYA jika pengeluaran ini terkait dengan alokasi bulanan yang sudah dibuat di `expenses_allocations`.
            $table->unsignedBigInteger('expense_allocation_id')->nullable();
            
            // Foreign key opsional untuk transaksi tabungan
            $table->unsignedBigInteger('saving_goal_id')->nullable();

            $table->timestamps();

            // Mendefinisikan foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('expenses_category_id')->references('id')->on('expenses_categories')->onDelete('set null'); 
            $table->foreign('expense_allocation_id')->references('id')->on('expenses_allocations')->onDelete('set null'); 
            $table->foreign('saving_goal_id')->references('id')->on('saving_goals')->onDelete('set null'); 

            // Indeks untuk pencarian cepat
            $table->index(['user_id', 'date']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};