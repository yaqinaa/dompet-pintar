// database/migrations/YYYY_MM_DD_create_allocation_categories_table.php
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
        Schema::create('allocation_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // 'Primer', 'Sekunder', 'Tersier', 'Tabungan'
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Seed initial data (disarankan pakai seeder terpisah, tapi bisa juga di sini)
        \DB::table('allocation_categories')->insert([
            ['name' => 'Primer', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sekunder', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tersier', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tabungan', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allocation_categories');
    }
};