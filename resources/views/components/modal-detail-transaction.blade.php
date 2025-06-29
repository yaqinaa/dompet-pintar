@push('styles')
<style>
    /* ============================================= */
    /* CSS UNTUK MODAL DETAIL TRANSAKSI */
    /* ============================================= */
    #detailTransactionModal .modal-dialog { max-width: 600px; }
    #detailTransactionModal .modal-content { border-radius: 1rem; border: none; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
    #detailTransactionModal .modal-header { background-color: #A3D959; color: #1F2937; border-bottom: none; padding: 1rem 1.5rem; }
    #detailTransactionModal .modal-header .modal-title { font-weight: 600; }
    
    /* PERBAIKAN: Kurangi padding bawah pada body */
    #detailTransactionModal .modal-body {
        padding: 2rem 2rem 1rem 2rem;
    }
    
    #detailTransactionModal .modal-body .row > div { margin-bottom: 1.25rem; }
    #detailTransactionModal .modal-body small.text-muted { font-size: 0.8rem; color: #6B7280 !important; display: block; margin-bottom: 0.25rem; }
    #detailTransactionModal .modal-body p.fw-bold { font-size: 1rem; font-weight: 600 !important; margin-bottom: 0; }
    #detailTransactionModal #detail-amount { font-size: 1.75rem; font-weight: 700 !important; }

    /* PERBAIKAN: Tambahkan garis pemisah tipis */
    #detailTransactionModal .modal-footer {
        border-top: 1px solid #E5E7EB; /* Garis pemisah */
        padding: 1rem 2rem 1.5rem 2rem; /* Atur padding agar tidak terlalu mepet garis */
        justify-content: flex-end;
    }

    /* CSS untuk Tombol Tutup */
    #detailTransactionModal .modal-footer .btn.btn-secondary {
        background-color: #E5E7EB; color: #374151; border: none;
        font-size: 0.875rem; padding: 0.5rem 1.25rem;
        border-radius: 0.75rem; font-weight: 600;
        transition: background-color 0.2s ease-in-out;
    }

    #detailTransactionModal .modal-footer .btn.btn-secondary:hover {
        background-color: #D1D5DB; color: #1F2937;
    }
</style>
@endpush
{{-- MODAL DETAIL TRANSAKSI (Versi Rapi) --}}
<div class="modal fade" id="detailTransactionModal" tabindex="-1" aria-labelledby="detailTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailTransactionModalLabel">Detail Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <small class="text-muted">Nama Transaksi</small>
                        <p class="fw-bold" id="detail-description"></p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Tanggal</small>
                        <p class="fw-bold" id="detail-date"></p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Kategori</small>
                        <p class="fw-bold" id="detail-category"></p>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Jenis Transaksi</small>
                        <p class="fw-bold" id="detail-type"></p>
                    </div>
                    <div class="col-12">
                        <small class="text-muted">Nominal</small>
                        <p class="fw-bold" id="detail-amount"></p>
                    </div>
                    
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>