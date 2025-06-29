// database/migrations/YYYY_MM_DD_create_expenses_allocations_table.php
// (Pastikan ini adalah versi terbaru setelah Anda melakukan `php artisan migrate:rollback` jika sudah ter-migrate)

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
        Schema::create('expenses_allocations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('income_allocation_id');
            $table->unsignedBigInteger('allocation_category_id'); // FK ke allocation_categories (Primer/Sekunder/Tersier)
            $table->string('name'); // Nama alokasi spesifik yang diinput manual (contoh: "Makan Sehari-hari")
            $table->bigInteger('allocated_amount'); // Jumlah dana yang dialokasikan untuk nama ini
            $table->timestamps();

            // Mendefinisikan foreign key
            $table->foreign('income_allocation_id')->references('id')->on('income_allocations')->onDelete('cascade');
            $table->foreign('allocation_category_id')->references('id')->on('allocation_categories')->onDelete('cascade');

            // Menggunakan nama indeks kustom agar nama unik tidak terlalu panjang
            // Pastikan nama alokasi unik per income_allocation_id dan allocation_category_id
            $table->unique(['income_allocation_id', 'allocation_category_id', 'name'], 'inc_alloc_cat_name_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses_allocations');
    }
};