<!-- Modal Tabung -->
<div class="modal fade" id="tabungModal" tabindex="-1" aria-labelledby="tabungModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tabungModalLabel">Tambah Tabungan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="tabungForm" method="POST" >
                    @csrf
                    <div class="mb-3">
                        <label for="amount" class="form-label">Jumlah Tabungan</label>
                        <input type="number" class="form-control" id="amount" name="amount" placeholder="Jumlah tabungan" required>
                    </div>

                    <div class="mb-3">
                        <label for="source_type" class="form-label">Sumber Dana</label>
                        <select class="form-control" name="source_type" id="source_type" required>
                            <option value="" disabled selected>Silahkan memilih</option>
                            <option value="alokasi">Dari Dana Alokasi</option>
                            <option value="tambahan">Dari Dana Tambahan</option>
                        </select>

                        <small id="alokasiInfo" class="text-success mt-1 d-none">
                            ✅ Anda sudah menabung dari dana alokasi bulan ini
                        </small>
                    </div>


                    <input type="hidden" name="goal_id" id="goal_id">
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="submit" form="tabungForm" class="btn btn-primary">Simpan</button>
            </div>
        </div>
    </div>
</div>
