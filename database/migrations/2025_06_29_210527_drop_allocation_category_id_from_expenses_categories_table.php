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
                    // Anda bisa mencatat log di sini jika perlu.
                    // Log::info("Foreign key untuk allocation_category_id tidak ditemukan atau sudah dihapus.");
                }

                // Setelah mencoba menghapus foreign key (atau gagal dengan aman), hapus kolomnya.
                $table->dropColumn('allocation_category_id');
            }

        });

    }
}