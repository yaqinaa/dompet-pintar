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
            // MENAMBAHKAN USER_ID SECARA LANGSUNG SEPERTI YANG ANDA INGINKAN
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); 

            $table->unsignedBigInteger('income_allocation_id');
            $table->unsignedBigInteger('allocation_category_id'); 
            $table->string('name'); 
            $table->bigInteger('allocated_amount'); 
            $table->timestamps();

            // Mendefinisikan foreign key
            $table->foreign('income_allocation_id')->references('id')->on('income_allocations')->onDelete('cascade');
            $table->foreign('allocation_category_id')->references('id')->on('allocation_categories')->onDelete('cascade');

            // MENYESUAIKAN UNIQUE CONSTRAINT:
            // Nama alokasi harus unik untuk setiap user.
            // Ini lebih masuk akal daripada unik per kombinasi income_allocation_id dan category_id
            $table->unique(['user_id', 'name'], 'user_expense_name_unique'); 
            
            // Catatan: Jika Anda ingin 'name' unik per kombinasi 'user_id', 'income_allocation_id', 'allocation_category_id', 
            // maka unique constraint-nya akan menjadi:
            // $table->unique(['user_id', 'income_allocation_id', 'allocation_category_id', 'name'], 'user_inc_alloc_cat_name_unique');
            // Namun, 'user_expense_name_unique' lebih umum dan sederhana.
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