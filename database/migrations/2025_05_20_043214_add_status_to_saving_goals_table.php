<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('saving_goals', function (Blueprint $table) {
            $table->enum('status', ['belum tercapai', 'tercapai'])->default('belum tercapai');
            // Alternatif: pakai boolean
            // $table->boolean('is_completed')->default(false);
        });
    }

    public function down()
    {
        Schema::table('saving_goals', function (Blueprint $table) {
            $table->dropColumn('status');
            // $table->dropColumn('is_completed');
        });
    }
};
