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
        Schema::create('income_allocations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Foreign key ke tabel users
            $table->bigInteger('dana_alokasi');  // Ganti total_pemasukan dengan dana_alokasi
            $table->integer('persen_primer');
            $table->integer('persen_sekunder');
            $table->integer('persen_tersier');
            $table->integer('persen_tabungan');
            $table->date('tanggal');
            $table->timestamps();

            // foreign key ke tabel users
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('income_allocations');
    }
};
