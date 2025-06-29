// database/migrations/YYYY_MM_DD_create_expenses_categories_table.php
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
        Schema::create('expenses_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('allocation_category_id'); // Foreign key ke tabel allocation_categories
            $table->string('name'); // Contoh: 'Belanja Bulanan', 'Transportasi', 'Makan di Luar', 'Investasi', 'Edukasi'
            $table->unsignedBigInteger('user_id')->nullable(); // Jika expenses_category bisa dibuat per user
            $table->timestamps();

            $table->foreign('allocation_category_id')->references('id')->on('allocation_categories')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            $table->unique(['allocation_category_id', 'name', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses_categories');
    }
};