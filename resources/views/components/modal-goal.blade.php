<!-- Modal Buat Goal Baru -->
<div class="modal fade" id="goalModal" tabindex="-1" aria-labelledby="goalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="goalModalLabel">Buat Saving Goal</h5>
                <!-- Tombol Close (menutup modal) -->
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="{{ route('saving-goals.store') }}" id="goalForm">
                    @csrf
                    <div class="mb-3">
                        <label for="goal_name" class="form-label">Nama Tujuan</label>
                        <input type="text" class="form-control" name="goal_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="target_amount" class="form-label">Target Jumlah (Rp)</label>
                        <input type="text" class="form-control" name="target_amount" id="target_amount" required>
                    </div>
                    <div class="mb-3">
                        <label for="deadline" class="form-label">Deadline</label>
                        <input type="date" class="form-control" name="deadline" required>
                    </div>
                    <div class="modal-footer">
                        <!-- Tombol Batal (Menutup Modal) -->
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <!-- Tombol Simpan (Mengirim Form) -->
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const targetAmountInput = document.getElementById('target_amount');

    // Menambahkan event listener untuk mengubah format input saat pengguna mengetik
    targetAmountInput.addEventListener('input', function (e) {
        // Ambil nilai input dan hanya izinkan karakter angka
        let value = e.target.value.replace(/[^0-9]/g, '');  // Menghapus karakter selain angka
        
        // Format angka dengan pemisah ribuan
        value = value.replace(/\B(?=(\d{3})+(?!\d))/g, '.');  // Tambahkan titik setiap 3 digit
        
        // Setel kembali nilai input dengan format Rupiah
        e.target.value = value ? `Rp ${value}` : '';  // Tampilkan dengan simbol 'Rp' jika ada nilai
    });

    // Sebelum form disubmit, pastikan kita hanya mengirimkan angka murni ke server
    document.getElementById('goalForm').addEventListener('submit', function (e) {
        // Ambil nilai dari input target_amount
        const targetAmount = targetAmountInput.value;

        // Hapus simbol 'Rp' dan titik pemisah ribuan sebelum submit
        const numericValue = targetAmount.replace(/[^\d]/g, '');  // Ambil angka murni tanpa simbol dan titik

        // Update nilai input target_amount untuk dikirim ke server
        targetAmountInput.value = numericValue;
    });
</script>
