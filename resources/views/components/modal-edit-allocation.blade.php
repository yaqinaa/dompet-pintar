{{-- Kita akan menggunakan style yang sama dengan modal tambah --}}
{{-- Anda bisa membuat file CSS terpisah atau menyalin style jika perlu --}}

@if ($allocation) {{-- Pastikan modal ini hanya dirender jika ada data alokasi --}}
<div class="modal fade modal-alloc" id="editDataModal" tabindex="-1" aria-labelledby="editDataLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form action="{{ route('income-allocations.update', $allocation->id) }}" method="POST">
                @csrf
                @method('PATCH') {{-- atau PUT --}}
                
                <div class="modal-header d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="modal-title" id="editDataLabel">Edit Alokasi Dana</h5>
                        <p class="header-subtitle mb-0">Ubah informasi alokasi untuk bulan ini</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>

                <div class="modal-body">
                    {{-- Bulan (biasanya tidak bisa diubah saat edit) --}}
                    <div class="mb-3">
                        <label for="tanggal_edit" class="form-label">Bulan</label>
                        <input type="month" class="form-control" id="tanggal_edit" name="tanggal" value="{{ \Carbon\Carbon::parse($allocation->tanggal)->format('Y-m') }}" readonly>
                    </div>

                    {{-- Total Pemasukan --}}
                    <div class="mb-3">
                        <label for="dana_alokasi_display_edit" class="form-label">Dana Alokasi</label>
                        <input type="text" class="form-control" id="dana_alokasi_display_edit" value="{{ number_format($allocation->dana_alokasi, 0, ',', '.') }}" required>
                        <input type="hidden" name="dana_alokasi" id="dana_alokasi_edit" value="{{ $allocation->dana_alokasi }}">
                    </div>

                    {{-- Alokasi Persentase --}}
                    <div class="row g-3">
                        <div class="col-6">
                            <label for="persen_primer_edit" class="form-label">Primer (%)</label>
                            <input type="number" class="form-control" id="persen_primer_edit" name="persen_primer" value="{{ $allocation->persen_primer }}" required>
                        </div>
                        <div class="col-6">
                            <label for="persen_sekunder_edit" class="form-label">Sekunder (%)</label>
                            <input type="number" class="form-control" id="persen_sekunder_edit" name="persen_sekunder" value="{{ $allocation->persen_sekunder }}" required>
                        </div>
                        <div class="col-6">
                            <label for="persen_tersier_edit" class="form-label">Tersier (%)</label>
                            <input type="number" class="form-control" id="persen_tersier_edit" name="persen_tersier" value="{{ $allocation->persen_tersier }}" required>
                        </div>
                        <div class="col-6">
                            <label for="persen_tabungan_edit" class="form-label">Tabungan (%)</label>
                            <input type="number" class="form-control" id="persen_tabungan_edit" name="persen_tabungan" value="{{ $allocation->persen_tabungan }}" readonly>
                        </div>
                    </div>

                    <div id="warningPersen_edit" class="alert alert-danger p-2 mt-3" style="display:none; font-size: 0.8rem;">
                        Total persentase tidak boleh melebihi 100%.
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- Script khusus untuk Modal Edit --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEdit = document.getElementById('editDataModal');
    if (!modalEdit) return;

    // --- Ambil semua elemen form di dalam scope modal EDIT ---
    const displayInputEdit = modalEdit.querySelector('#dana_alokasi_display_edit');
    const hiddenInputEdit = modalEdit.querySelector('#dana_alokasi_edit');
    const primerInputEdit = modalEdit.querySelector('#persen_primer_edit');
    const sekunderInputEdit = modalEdit.querySelector('#persen_sekunder_edit');
    const tersierInputEdit = modalEdit.querySelector('#persen_tersier_edit');
    const tabunganInputEdit = modalEdit.querySelector('#persen_tabungan_edit');
    const warningElEdit = modalEdit.querySelector('#warningPersen_edit');
    const submitBtnEdit = modalEdit.querySelector('button[type="submit"]');

    function updateEditCalculations() {
        const total = parseInt(hiddenInputEdit.value.replace(/[^0-9]/g, '')) || 0;
        const primer = parseInt(primerInputEdit.value) || 0;
        const sekunder = parseInt(sekunderInputEdit.value) || 0;
        const tersier = parseInt(tersierInputEdit.value) || 0;
        const totalPersen = primer + sekunder + tersier;
        
        if (totalPersen > 100) {
            tabunganInputEdit.value = '';
            warningElEdit.style.display = 'block';
            submitBtnEdit.disabled = true;
        } else {
            const sisaPersen = 100 - totalPersen;
            tabunganInputEdit.value = sisaPersen;
            warningElEdit.style.display = 'none';
            submitBtnEdit.disabled = false;
        }
    }

    // --- Event Listener untuk input Rupiah ---
    displayInputEdit.addEventListener('input', function(e) {
        let value = e.target.value.replace(/[^0-9]/g, '');
        hiddenInputEdit.value = value;
        e.target.value = value ? new Intl.NumberFormat('id-ID').format(value) : '';
        updateEditCalculations();
    });

    // --- Event Listener untuk input persentase ---
    [primerInputEdit, sekunderInputEdit, tersierInputEdit].forEach(input => {
        input.addEventListener('input', updateEditCalculations);
    });

    
});
</script>
@endpush