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
        Schema::create('goal_allocations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('income_allocation_id'); // WAJIB, karena ini pembagian dari income allocation
            $table->unsignedBigInteger('saving_goal_id');
            $table->bigInteger('amount'); // jumlah dana dialokasikan ke goal
            $table->timestamps();

            // Foreign keys
            $table->foreign('income_allocation_id')->references('id')->on('income_allocations')->onDelete('cascade');
            $table->foreign('saving_goal_id')->references('id')->on('saving_goals')->onDelete('cascade');

            // Unik constraint untuk memastikan alokasi per bulan per goal
            $table->unique(['income_allocation_id', 'saving_goal_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goal_allocations');
    }
};
