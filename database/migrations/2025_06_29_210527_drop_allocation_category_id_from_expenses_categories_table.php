<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log; // Tambahkan ini jika ingin menggunakan Log

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Hanya jalankan perubahan jika tabel 'expenses_categories' ada
        if (Schema::hasTable('expenses_categories')) {
            
            Schema::table('expenses_categories', function (Blueprint $table) {

                // Cek dulu jika kolom 'allocation_category_id' memang ada di tabel
                if (Schema::hasColumn('expenses_categories', 'allocation_category_id')) {

                    try {
                        // Coba hapus foreign key dengan nama default Laravel
                        $table->dropForeign(['allocation_category_id']);
                    } catch (\Exception $e) {
                        // Jika gagal (karena nama beda atau tidak ada), abaikan error dan lanjutkan.
                        // Anda bisa mencatat log di sini jika perlu untuk debugging di server.
                        // Log::info("Foreign key untuk allocation_category_id tidak ditemukan atau sudah dihapus saat migrasi.");
                    }

                    // Setelah mencoba menghapus foreign key (atau gagal dengan aman), hapus kolomnya.
                    $table->dropColumn('allocation_category_id');
                }

            });

        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Fungsi ini untuk mengembalikan kolom jika migrasi di-rollback (best practice)
        Schema::table('expenses_categories', function (Blueprint $table) {
            // Cek jika kolomnya belum ada, baru tambahkan
            if (!Schema::hasColumn('expenses_categories', 'allocation_category_id')) {
                $table->foreignId('allocation_category_id')
                      ->nullable()
                      ->after('user_id') // Letakkan setelah kolom user_id
                      ->constrained('allocation_categories') // Ganti jika nama tabel alokasi Anda berbeda
                      ->onDelete('set null');
            }
        });
    }
};