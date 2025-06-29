<style>
    /* CSS Kustom untuk Modal Tambah Alokasi */
    .modal-alloc .modal-content {
        border-radius: 12px;
        border: none;
        box-shadow: 0 8px 32px rgba(0,0,0,0.1);
    }
    .modal-alloc .modal-header {
        padding: 1.5rem 1.5rem 1rem 1.5rem;
        /* PERBAIKAN 1: Tambahkan garis bawah */
        border-bottom: 1px solid #dee2e6;
    }
    .modal-alloc .modal-header .modal-title {
        font-weight: 600;
        font-size: 1.25rem;
    }
    .modal-alloc .modal-header .header-subtitle {
        font-size: 0.9rem;
        color: #6c757d;
    }
    .modal-alloc .modal-body {
        padding: 1rem;
    }
    .modal-alloc .form-label {
        font-size: 0.875rem;
        font-weight: 500;
        margin-bottom: 0.1rem;
        color: #495057;
    }
    .modal-alloc .form-control {
        border-radius: 8px;
        /* PERBAIKAN 2: Background dibuat putih (transparan) */
        background-color: transparent; 
        border: 1px solid #DEE2E6;
        padding: 0.5rem 1rem;
    }
    .modal-alloc .form-control:focus {
        background-color: white;
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    .modal-alloc .form-check {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0;
    }
    .modal-alloc .form-check-label {
        font-weight: 500;
    }
    .modal-alloc .modal-footer {
        padding: 1rem 1.5rem 1.5rem 1.5rem;
        /* PERBAIKAN 1: Tambahkan garis atas */
        border-top: 1px solid #dee2e6;
    }
    .modal-alloc .modal-footer .btn {
        border-radius: 8px;
        font-weight: 500;
        padding: 0.6rem 1.5rem;
    }
    

    /* ======================================================== */
    /* == CSS UNTUK MEMPERTEGAS BORDER CHECKBOX == */
    /* ======================================================== */

    .modal-alloc .form-check-input {
        /* Sedikit perbesar ukuran checkbox */
        width: 1.15em;
        height: 1.15em;
        
        /* Ini adalah bagian kuncinya */
        border: 1px solid #adb5bd; /* Ganti warna border default yang samar */
    }

    /* Style saat checkbox di-fokus (diklik atau di-tab) */
    .modal-alloc .form-check-input:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    /* Style saat checkbox sudah dicentang */
    .modal-alloc .form-check-input:checked {
        background-color: #0d6efd; /* Warna background saat dicentang */
        border-color: #0d6efd;
    }
</style>

<div class="modal fade modal-alloc" id="tambahDataModal" tabindex="-1" aria-labelledby="tambahDataLabel" aria-hidden="true">
    {{-- PERBAIKAN 3: Tambahkan class modal-dialog-scrollable --}}
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form action="{{ route('income-allocations.store') }}" method="POST">
                @csrf
                <div class="modal-header d-flex justify-content-between align-items-start">
                    {{-- Grup Judul dan Subjudul --}}
                    <div>
                        <h5 class="modal-title" id="tambahDataLabel">Tambah Alokasi Dana</h5>
                        <p class="header-subtitle mb-0">Isi informasi berikut untuk menambahkan Alokasi</p>
                    </div>
                    {{-- Tombol Close --}}
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                
                <div class="modal-body">
                    {{-- Opsi Gunakan Data Bulan Sebelumnya --}}
                    @if($previousAllocations->isNotEmpty())
                    <div class="mb-3 form-check">
                        <label class="form-check-label" for="copyFromCheckbox">Gunakan Data bulan Sebelumnya (opsional)</label>
                        <input class="form-check-input" type="checkbox" id="copyFromCheckbox">
                    </div>
                    @endif

                    {{-- Bulan --}}
                    <div class="mb-3">
                        <label for="tanggal" class="form-label">Bulan</label>
                        <input type="month" class="form-control" id="tanggal" name="tanggal" value="{{ $filterDate ?? \Carbon\Carbon::now()->format('Y-m') }}" readonly required>
                    </div>

                    {{-- Dana Alokasi --}}
                    <div class="mb-3">
                        <label for="dana_alokasi_display" class="form-label">Dana Alokasi</label>
                        <input type="text" class="form-control" id="dana_alokasi_display" placeholder="Rp 0" required>
                        <input type="hidden" name="dana_alokasi" id="dana_alokasi">
                    </div>

                <div class="row g-3"> {{-- g-3 adalah gutter/jarak antar kolom --}}
                {{-- BARIS PERTAMA --}}
                <div class="col-6">
                    <label for="persen_primer" class="form-label">Primer (%)</label>
                    <input type="number" class="form-control" id="persen_primer" name="persen_primer" placeholder="0" required>
                </div>
                <div class="col-6">
                    <label for="persen_sekunder" class="form-label">Sekunder (%)</label>
                    <input type="number" class="form-control" id="persen_sekunder" name="persen_sekunder" placeholder="0" required>
                </div>

                {{-- BARIS KEDUA --}}
                <div class="col-6">
                    <label for="persen_tersier" class="form-label">Tersier (%)</label>
                    <input type="number" class="form-control" id="persen_tersier" name="persen_tersier" placeholder="0" required>
                </div>
                <div class="col-6">
                    <label for="persen_tabungan" class="form-label">Tabungan (%)</label>
                    <input type="number" class="form-control" id="persen_tabungan" name="persen_tabungan" placeholder="Sisa" readonly>
                </div>
            </div>

                    <div id="warningPersen" class="alert alert-danger p-2 mt-2" style="display:none; font-size: 0.8rem;">
                        Total persentase tidak boleh melebihi 100%.
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- Mempertahankan semua script fungsional Anda, dengan sedikit penyesuaian --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('tambahDataModal');
    if (!modal) return;

    // --- Ambil semua elemen form di dalam scope modal ---
    const form = modal.querySelector('form');
    const displayInput = modal.querySelector('#dana_alokasi_display');
    const hiddenInput = modal.querySelector('#dana_alokasi');
    const primerInput = modal.querySelector('#persen_primer');
    const sekunderInput = modal.querySelector('#persen_sekunder');
    const tersierInput = modal.querySelector('#persen_tersier');
    const tabunganInput = modal.querySelector('#persen_tabungan');
    const warningEl = modal.querySelector('#warningPersen');
    const submitBtn = modal.querySelector('button[type="submit"]');
    const copyCheckbox = modal.querySelector('#copyFromCheckbox');
    
    // --- Data bulan sebelumnya (jika ada) ---
    const previousAllocationData = @json(
    ($previousAllocations->isNotEmpty()) ? $previousAllocations->first()->first() : null
);

    // --- Fungsi Kalkulasi ---
    function updateCalculations() {
        const total = parseInt(hiddenInput.value) || 0;
        const primer = parseInt(primerInput.value) || 0;
        const sekunder = parseInt(sekunderInput.value) || 0;
        const tersier = parseInt(tersierInput.value) || 0;
        const totalPersen = primer + sekunder + tersier;
        
        if (totalPersen > 100) {
            tabunganInput.value = '';
            warningEl.style.display = 'block';
            submitBtn.disabled = true;
        } else {
            const sisaPersen = 100 - totalPersen;
            tabunganInput.value = sisaPersen;
            warningEl.style.display = 'none';
            submitBtn.disabled = false;
        }
    }

    // --- Event Listener untuk input Rupiah ---
    displayInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/[^0-9]/g, '');
        hiddenInput.value = value;
        e.target.value = value ? new Intl.NumberFormat('id-ID').format(value) : '';
        updateCalculations();
    });

    // --- Event Listener untuk input persentase ---
    [primerInput, sekunderInput, tersierInput].forEach(input => {
        input.addEventListener('input', updateCalculations);
    });

    if (copyCheckbox) {
    copyCheckbox.addEventListener('change', function() {
        if (this.checked && previousAllocationData) {
            // Data sekarang adalah objek langsung, bukan array
            
            // Isi total pemasukan
            displayInput.value = new Intl.NumberFormat('id-ID').format(previousAllocationData.dana_alokasi);
            hiddenInput.value = previousAllocationData.dana_alokasi;

            // Isi persentase
            primerInput.value = previousAllocationData.persen_primer;
            sekunderInput.value = previousAllocationData.persen_sekunder;
            tersierInput.value = previousAllocationData.persen_tersier;
            
            // Panggil kalkulasi
            updateCalculations();
        } else {
            // Kosongkan form
            form.reset();
            hiddenInput.value = '';
            displayInput.value = '';
            updateCalculations();
        }
    });
}


    // --- Reset form saat modal ditutup ---
    modal.addEventListener('hidden.bs.modal', function () {
        form.reset();
        hiddenInput.value = '';
        displayInput.placeholder = 'Rp 0';
        updateCalculations();
    });
});
</script>
@endpush