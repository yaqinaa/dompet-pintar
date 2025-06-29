{{-- Modal expenseAllocationModal --}}
<style>
    /* ... (CSS Anda untuk modal expenseAllocationModal, tidak ada perubahan) ... */
</style>

<div class="modal fade" id="expenseAllocationModal" tabindex="-1" aria-labelledby="expenseAllocationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="expenseAllocationModalLabel">
                    Alokasi Pengeluaran - Kategori: <span id="modalCategoryName"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if ($allocation)
                    <form action="{{ route('expenses.allocations.store') }}" method="POST" id="expense-allocation-form">
                        @csrf
                        {{-- Hidden inputs --}}
                        <input type="hidden" name="income_allocation_id" value="{{ $allocation->id }}">
                        <input type="hidden" name="allocation_category_id" id="modalAllocationCategoryId">
                        {{-- Total nominal kategori akan diisi JS --}}
                        <input type="hidden" id="initialAllocatedNominal">

                        {{-- PERUBAHAN DI SINI: Jadikan 1 baris 2 kolom --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Dana Tersedia untuk Kategori Ini:</label>
                                <p class="form-control-plaintext" id="modalAllocatedNominalDisplay">Rp 0</p>
                                <small class="text-muted">Ini adalah total dana yang Anda alokasikan untuk kategori <span id="modalCategoryNameSmall"></span> </small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Sisa Dana Kategori:</label>
                                <p class="form-control-plaintext h5" id="remainingCategoryAmount">Rp 0</p>
                            </div>
                        </div>
                        {{-- AKHIR PERUBAHAN --}}

                        <hr>

                        <label class="form-label fw-bold">Rincian Pengeluaran:</label>
                        <div id="allocationRowsContainer">
                            {{-- BARU: Loop melalui semua existing expenses, tapi hanya tampilkan yang relevan dengan kategori yang sedang aktif --}}
                            {{-- Logika ini akan dijalankan saat modal dibuka oleh JavaScript --}}
                        </div>

                        <button type="button" id="addAllocationRow" class="btn btn-outline-secondary btn-sm mt-3">
                            <i class="bi bi-plus-circle"></i> Tambah Baris Pengeluaran
                        </button>

                        <div id="expenseAllocationWarning" class="alert alert-danger d-none mt-3" role="alert">
                            ⚠️ **Total alokasi pengeluaran melebihi dana yang tersedia!** Harap sesuaikan jumlahnya.
                        </div>

                    </form>
                @else
                    <div class="alert alert-info">Belum ada data alokasi pemasukan untuk bulan ini. Harap tambahkan terlebih dahulu.</div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary me-auto" id="submitExpenseAllocation">Simpan Alokasi</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const expenseAllocationModal = document.getElementById('expenseAllocationModal');
    if (!expenseAllocationModal) {
        console.warn("Modal with ID 'expenseAllocationModal' not found.");
        return;
    }

    const allocationRowsContainer = document.getElementById('allocationRowsContainer');
    const addAllocationRowButton = document.getElementById('addAllocationRow');
    const submitExpenseAllocationButton = document.getElementById('submitExpenseAllocation');
    const expenseAllocationWarning = document.getElementById('expenseAllocationWarning');
    const remainingCategoryAmountDisplay = document.getElementById('remainingCategoryAmount');
    const initialAllocatedNominalInput = document.getElementById('initialAllocatedNominal');
    const expenseAllocationForm = document.getElementById('expense-allocation-form');

    let rowCounter = 0; // Untuk ID dan nama input yang unik, direset setiap modal dibuka

    // Data kategori dan alokasi yang sudah ada (diambil dari Blade)
    const bladeDataKategori = @json($dataKategori ?? []);
    const allExistingExpenseAllocations = @json($allExistingExpenseAllocations ?? []);

    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(number);
    }

    function unformatRupiah(rpString) {
        return parseInt(rpString.replace(/[^0-9]/g, '')) || 0;
    }

    function updateRemainingAmount() {
        let totalAllocatedInModal = 0;
        document.querySelectorAll('#allocationRowsContainer .expense-amount-input').forEach(input => {
            totalAllocatedInModal += unformatRupiah(input.value);
        });

        const initialNominal = parseInt(initialAllocatedNominalInput.value) || 0;
        const remaining = initialNominal - totalAllocatedInModal;

        remainingCategoryAmountDisplay.textContent = formatRupiah(remaining);

        if (remaining < 0) {
            remainingCategoryAmountDisplay.classList.add('text-danger');
            expenseAllocationWarning.classList.remove('d-none');
            submitExpenseAllocationButton.disabled = true;
        } else {
            remainingCategoryAmountDisplay.classList.remove('text-danger');
            expenseAllocationWarning.classList.add('d-none');
            submitExpenseAllocationButton.disabled = false;
        }
    }

    function addAllocationRow(expense = null) {
        rowCounter++;

        const nameValue = expense ? expense.name : '';
        const amountValue = expense ? expense.allocated_amount : 0;
        const idValue = expense ? expense.id : '';

        const newRow = document.createElement('div');
        newRow.className = 'row mb-2 allocation-row';
        newRow.innerHTML = `
            <div class="col-md-6">
                <input type="hidden" name="allocations[${rowCounter}][id]" value="${idValue}">
                <input type="text"
                       class="form-control allocation-name-input"
                       name="allocations[${rowCounter}][name]"
                       placeholder="Nama pengeluaran (ex: Internet, Listrik)"
                       value="${nameValue}"
                       required>
            </div>
            <div class="col-md-5">
                <input type="text"
                       class="form-control expense-amount-input formatted-input"
                       name="allocations[${rowCounter}][allocated_amount]"
                       min="0"
                       value="${formatRupiah(amountValue)}"
                       placeholder="Rp 0"
                       required>
            </div>
            <div class="col-md-1 d-flex align-items-center justify-content-center">
                <button type="button" class="btn btn-danger btn-sm remove-allocation-row" title="Hapus baris"><i class="bi bi-x-circle"></i></button>
            </div>
        `;
        allocationRowsContainer.appendChild(newRow);

        const amountInput = newRow.querySelector('.expense-amount-input');
        amountInput.addEventListener('input', function() {
            const value = unformatRupiah(this.value);
            this.value = formatRupiah(value);
            updateRemainingAmount();
        });

        newRow.querySelector('.remove-allocation-row').addEventListener('click', function() {
            newRow.remove();
            updateRemainingAmount();
        });

        updateRemainingAmount();
    }

    // Event listener saat modal pengeluaran dibuka
    expenseAllocationModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget; // Tombol yang memicu modal
        const clickedCategoryId = button.getAttribute('data-category-id'); // Ambil ID kategori dari tombol

        // Reset form dan container
        allocationRowsContainer.innerHTML = '';
        rowCounter = 0;
        expenseAllocationWarning.classList.add('d-none');
        submitExpenseAllocationButton.disabled = false;

        // Cari informasi kategori berdasarkan ID yang diklik
        let categoryName = 'Tidak Diketahui';
        let categoryNominal = 0;
        let categoryFound = false;

        for (const key in bladeDataKategori) {
            if (bladeDataKategori.hasOwnProperty(key)) {
                const categoryInfo = bladeDataKategori[key];
                if (categoryInfo.allocation_category_id == clickedCategoryId) {
                    categoryName = key;
                    categoryNominal = categoryInfo.nominal;
                    categoryFound = true;
                    break;
                }
            }
        }
        
        if (!categoryFound) {
            console.error("Kategori dengan ID " + clickedCategoryId + " tidak ditemukan di dataKategori.");
        }

        // Isi elemen modal
        expenseAllocationModal.querySelector('#modalCategoryName').textContent = categoryName;
        expenseAllocationModal.querySelector('#modalCategoryNameSmall').textContent = categoryName;
        expenseAllocationModal.querySelector('#modalAllocatedNominalDisplay').textContent = formatRupiah(categoryNominal);
        expenseAllocationModal.querySelector('#initialAllocatedNominal').value = categoryNominal;
        expenseAllocationModal.querySelector('#modalAllocationCategoryId').value = clickedCategoryId;

        // Filter dan tampilkan alokasi pengeluaran yang sudah ada untuk kategori ini
        const relevantExpenses = allExistingExpenseAllocations.filter(expense =>
            expense.allocation_category_id == clickedCategoryId
        );

        if (relevantExpenses.length > 0) {
            relevantExpenses.forEach(expense => {
                addAllocationRow(expense);
            });
        } else {
            addAllocationRow(); // Tambahkan satu baris kosong jika belum ada alokasi untuk kategori ini
        }

        updateRemainingAmount(); // Pastikan sisa dana diperbarui saat modal dibuka
    });

    addAllocationRowButton.addEventListener('click', function() {
        addAllocationRow();
    });

    submitExpenseAllocationButton.addEventListener('click', function(e) {
        if (expenseAllocationForm) {
            expenseAllocationForm.submit();
        } else {
            console.error("Expense allocation form not found!");
        }
    });
});
</script>
@endpush