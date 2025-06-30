{{-- CSS Kustom hanya untuk Modal Detail --}}
<style>
    /* ======================================================== */
    /* == CSS FINAL UNTUK MODAL DETAIL (DENGAN HOVER AKTIF) == */
    /* ======================================================== */

    /* --- Base --- */
    .modal-detail .modal-dialog { max-width: 600px; }
    .modal-detail .modal-content { border-radius: 12px; border: 1px solid #E9ECEF; box-shadow: 0 8px 24px rgba(0,0,0,0.15); }
    .modal-detail .modal-header { background-color: var(--green-light, #D7F964); border-bottom: 1px solid var(--border-color, #E9ECEF); padding: 0.75rem 1.25rem; }
    .modal-detail .modal-title { font-weight: 600; font-size: 1.1rem; color: #1F2937; }
    .modal-detail .modal-body { padding: 1.25rem; }
    .modal-detail .modal-footer { border-top: 1px solid var(--border-color, #E9ECEF); padding: 0.75rem 1.25rem; display:flex; justify-content: space-between; }
    
    /* --- Konten Detail --- */
    .detail-item { margin-bottom: 0.8rem; }
    .detail-item .label { font-size: 0.75rem; color: #6B7280; margin-bottom: 0; }
    .detail-item .value { font-size: 1.1rem; font-weight: 600; color: #111827; margin: 0; }
    .detail-item .progress-value { font-size: 0.8rem; font-weight: 500; }
    .detail-item .progress { height: 10px; background-color: #E5E7EB; border-radius: 10px; }
    .detail-item .progress-bar { background-color: #16A34A; }
    .calculation-grid { display: grid; grid-template-columns: auto 1fr; gap: 0.25rem 1rem; font-size: 0.85rem; }

    /* --- Tombol Close (X) --- */
    .modal-detail .btn-close {
        background: none;
        color: #1F2937;
        font-size: 1rem;
        font-weight: 500;
        line-height: 1;
        opacity: 0.7;
        padding: 0;
        text-shadow: none;
        transition: opacity 0.2s;
    }
    .modal-detail .btn-close:hover {
        opacity: 1;
    }

    /* --- Style Dasar Semua Tombol di Footer --- */
    .modal-detail .modal-footer .btn {
        border-radius: 8px;
        font-weight: 600;
        padding: 0.5rem 1.25rem;
        font-size: 0.875rem;
        border: none;
        transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
    }

    /* ================================================= */
    /* == PERBAIKAN HOVER UNTUK SETIAP JENIS TOMBOL == */
    /* ================================================= */

    /* Tombol Edit (Oranye) */
    .modal-detail .btn-edit { background-color: #F97316; color: white; }
    .modal-detail .btn-edit:hover { background-color: #EA580C !important; } /* Oranye lebih gelap */

    /* Tombol Delete (Merah) */
    .modal-detail .btn-delete { background-color: #EF4444; color: white; }
    .modal-detail .btn-delete:hover { background-color: #DC2626 !important; } /* Merah lebih gelap */

    /* Tombol Tarik (Biru) */
    .modal-detail .btn-withdraw { background-color: #3B82F6; color: white; }
    .modal-detail .btn-withdraw:hover { background-color: #2563EB !important; } /* Biru lebih gelap */

    /* Tombol Tutup (Abu-abu) */
    .modal-detail .btn-close-footer { background-color: #E5E7EB; color: #374151; }
    .modal-detail .btn-close-footer:hover { background-color: #D1D5DB !important; } /* Abu-abu lebih gelap */
    
    /* Tombol Batal di Mode Edit */
    .modal-detail .actions-edit .btn-secondary { border: 1px solid #D1D5DB; background-color: #fff; color: #374151; }
    .modal-detail .actions-edit .btn-secondary:hover { background-color: #F3F4F6 !important; }

    /* Tombol Simpan di Mode Edit */
    .modal-detail .actions-edit .btn-primary { background-color: #16A34A; color: white; border-color: #16A34A; }
    .modal-detail .actions-edit .btn-primary:hover { background-color: #15803D !important; border-color: #15803D; }

</style>
{{-- Loop untuk setiap goal agar setiap modal unik --}}
@foreach($activeGoals as $goal)
    @php
        $progress = $goal->target_amount > 0 ? ($goal->saved_amount / $goal->target_amount) * 100 : 0;
    @endphp

    <div class="modal fade modal-detail" id="detailGoalModal{{ $goal->id }}" tabindex="-1" aria-labelledby="detailGoalModalLabel{{ $goal->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            {{-- PERBAIKAN: Hanya ada SATU div.modal-content --}}
            <div class="modal-content">
                
                {{-- ======================== BAGIAN TAMPILAN DETAIL (VIEW MODE) ======================== --}}
                <div class="view-mode">
                    <div class="modal-header">
                        <h5 class="modal-title">Detail Tabungan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
                    </div>
                    <div class="modal-body">
                        <div class="detail-item">
                            <p class="label mb-0">Nama Tujuan</p>
                            <p class="value">{{ $goal->goal_name }}</p>
                        </div>
                        <div class="detail-item">
                            <p class="label mb-0">Target Nominal</p>
                            <p class="value">Rp. {{ number_format($goal->target_amount, 0, ',', '.') }}</p>
                        </div>
                        <div class="detail-item">
                            <p class="label mb-0">Tabungan Terkumpul</p>
                            <p class="value">Rp. {{ number_format($goal->saved_amount, 0, ',', '.') }}</p>
                        </div>
                        <div class="detail-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <p class="label mb-0">Progress</p>
                                <span class="progress-value">{{ round($progress) }}%</span>
                            </div>
                            <div class="progress mt-1">
                                <div class="progress-bar" role="progressbar" style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}"></div>
                            </div>
                        </div>
                        <div class="detail-item">
                            <p class="label mb-0">Deadline</p>
                            <p class="value" style="font-size: 0.9rem;">{{ \Carbon\Carbon::parse($goal->deadline)->format('d F Y') }}</p>
                        </div>
                        <div class="calculation-grid pt-3 mt-3 border-top">
                            <span>Per Hari</span><span>: Rp. {{ number_format($goal->amount_per_day ?? 0, 0, ',', '.') }}</span>
                            <span>Per Bulan</span><span>: Rp. {{ number_format($goal->amount_per_month ?? 0, 0, ',', '.') }}</span>
                            <span>Per Tahun</span><span>: Rp. {{ number_format($goal->amount_per_year ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="actions-left">
                            @if (!$goal->is_archived)

                            <button class="btn btn-edit" onclick="toggleEditMode(this, true)">Edit</button>
                            @endif
                            <form action="{{ route('saving-goals.destroy', $goal->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-delete" onclick="return confirm('Yakin ingin menghapus goal ini?')">Delete</button>
                            </form>
                        </div>
                        <div class="actions-right">
                            <button type="button" class="btn btn-close-footer" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>

                {{-- ======================== BAGIAN FORM EDIT (EDIT MODE) ======================== --}}
                <div class="edit-mode" style="display:none;">
                    <form action="{{ route('saving-goals.update', $goal->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Tabungan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="goal_name_{{ $goal->id }}" class="form-label">Nama Tujuan</label>
                                <input type="text" class="form-control" id="goal_name_{{ $goal->id }}" name="goal_name" value="{{ $goal->goal_name }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="target_amount_display_{{ $goal->id }}" class="form-label">Target Nominal</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    
                                    {{-- 1. Input Teks yang dilihat pengguna --}}
                                    <input type="text" class="form-control" 
                                        id="target_amount_display_{{ $goal->id }}" 
                                        value="{{ number_format($goal->target_amount, 0, ',', '.') }}" 
                                        onkeyup="formatRupiah(this, 'target_amount_hidden_{{ $goal->id }}')" 
                                        required>
                                        
                                    {{-- 2. Input Tersembunyi yang menyimpan angka murni dan di-submit ke server --}}
                                    <input type="hidden" 
                                        name="target_amount" 
                                        id="target_amount_hidden_{{ $goal->id }}" 
                                        value="{{ $goal->target_amount }}">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="deadline_{{ $goal->id }}" class="form-label">Deadline</p>
                                <input type="date" class="form-control" id="deadline_{{ $goal->id }}" name="deadline" value="{{ \Carbon\Carbon::parse($goal->deadline)->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="actions-left">
                                <button type="button" class="btn btn-secondary" onclick="toggleEditMode(this, false)">Batal</button>
                            </div>
                            <div class="actions-right">
                                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="withdraw-mode" style="display:none;">
                    <form action="#" method="POST">
                        @csrf
                        @method('POST')
                        <div class="modal-header"><h5 class="modal-title">Tarik Dana dari "{{ $goal->goal_name }}"</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                        <div class="modal-body">
                             <div class="mb-3"><p class="available-balance">Saldo Tersedia: <strong>Rp. {{ number_format($goal->saved_amount, 0, ',', '.') }}</strong></p></div>
                            <div class="mb-3">
                                <label for="withdraw_amount_display_{{ $goal->id }}" class="form-label">Jumlah Penarikan</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="text" class="form-control" id="withdraw_amount_display_{{ $goal->id }}" placeholder="0" onkeyup="formatRupiah(this, 'withdraw_amount_hidden_{{ $goal->id }}')" required>
                                    <input type="hidden" name="amount" id="withdraw_amount_hidden_{{ $goal->id }}">
                                </div>
                                <div class="form-text">Progres tabungan Anda akan berkurang setelah penarikan.</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="actions-left"><button type="button" class="btn btn-secondary" onclick="toggleWithdrawMode(this, false)">Batal</button></div>
                            <div class="actions-right"><button type="submit" class="btn btn-danger">Konfirmasi Penarikan</button></div>
                        </div>
                    </form>
                </div>
                
            </div>
        </div>
    </div>
@endforeach
