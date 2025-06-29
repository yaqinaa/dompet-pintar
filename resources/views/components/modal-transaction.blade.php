{{-- MODAL TAMBAK TRANSAKSI --}}
<div class="modal fade modal-trans" id="addTransactionModal" tabindex="-1" aria-labelledby="addTransactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg"> {{-- Tambahkan modal-lg untuk memberi ruang lebih --}}
        <div class="modal-content">
            <form id="transactionForm" method="POST" action="{{ route('transactions.store') }}">
                @csrf
                <div class="modal-header d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="modal-title" id="addTransactionModalLabel">Tambah Transaksi Baru</h5>
                        <p class="header-subtitle mb-0">Isi detail transaksi di bawah ini.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    {{-- Menampilkan pesan error validasi (tetap di atas) --}}
                    @if ($errors->any())
                        <div class="alert alert-danger mb-3">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Mulai sistem grid dengan 2 kolom --}}
                    <div class="row">

                        {{-- ======================= KOLOM 1 ======================= --}}
                        <div class="col-md-6">
                            
                            {{-- Jenis Transaksi --}}
                            <div class="mb-3">
                                <label for="transaction_type" class="form-label">Jenis Transaksi</label>
                                <select class="form-select @error('type') is-invalid @enderror" id="transaction_type" name="type" required>
                                    <option value="">Pilih Jenis</option>
                                    <option value="income" {{ old('type') == 'income' ? 'selected' : '' }}>Pemasukan</option>
                                    <option value="expense" {{ old('type') == 'expense' ? 'selected' : '' }}>Pengeluaran</option>
                                    <option value="saving_deposit" {{ old('type') == 'saving_deposit' ? 'selected' : '' }}>Setor Tabungan</option>
                                    <option value="saving_withdrawal" {{ old('type') == 'saving_withdrawal' ? 'selected' : '' }}>Tarik Tabungan</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- Grup untuk Kategori Pengeluaran (Hanya untuk tipe 'expense') --}}
                            <div class="mb-3" id="expensesCategoryGroup">
                                <label for="expenses_category_id_general" class="form-label">Kategori</label>
                                <select class="form-select @error('expenses_category_id') is-invalid @enderror" id="expenses_category_id_general" name="expenses_category_id">
                                    <option value="">Pilih Kategori</option>
                                    @foreach($expenseCategories as $category)
                                        <option value="{{ $category->id }}" {{ old('expenses_category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                    <option value="add_new_category">-- Tambah Kategori Baru --</option>
                                </select>
                                
                                @error('expenses_category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                {{-- Input untuk Kategori Baru (tersembunyi) --}}
                                <div id="newCategoryInputGroup" class="input-group mt-2 d-none">
                                    <input type="text" class="form-control @error('new_category_name') is-invalid @enderror" id="new_category_name_input" placeholder="Nama Kategori Baru" value="{{ old('new_category_name') }}">
                                    <button class="btn btn-outline-secondary" type="button" id="cancelNewCategory">Batal</button>
                                    @error('new_category_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
        
                            {{-- Grup Kategori untuk Tabungan (Hanya untuk tipe 'saving_*') --}}
                            <div class="mb-3 d-none" id="savingCategoryGroup">
                                <label for="saving_category_display" class="form-label">Kategori</label>
                                <input type="text" class="form-control" id="saving_category_display" value="Tabungan" readonly disabled>
                                <div class="form-text">Kategori "Tabungan" akan digunakan secara otomatis.</div>
                            </div>

                            <div id="savingGoalBalanceInfo" class="alert alert-info p-2" style="display:none; font-size: 0.9rem;">
                                Saldo Tersedia: <strong id="balanceAmount">Rp 0</strong>
                            </div>

                        </div>

                        {{-- ======================= KOLOM 2 ======================= --}}
                        <div class="col-md-6">

                            {{-- Grup Nama Transaksi (dinamis) --}}
                            <div class="mb-3" id="namaTransaksiGroup">
                                <label for="description_text_input" class="form-label" id="namaTransaksiLabel">Nama Transaksi</label>
                                <div id="dynamicNamaTransaksiContent">
                                    {{-- Konten dinamis akan di-render oleh JavaScript di sini --}}
                                </div>
                                {{-- Tampilkan semua kemungkinan error di sini --}}
                                @error('description')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                @error('expense_allocation_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                @error('saving_goal_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
        
                            {{-- Jumlah --}}
                            <div class="mb-3">
                                <label for="transaction_amount_display" class="form-label">Jumlah</label>
                                
                                {{-- 1. Input yang terlihat oleh user (format Rupiah) --}}
                                <input type="text" 
                                    class="form-control @error('amount') is-invalid @enderror" 
                                    id="transaction_amount_display" 
                                    placeholder="Rp 0">

                                {{-- 2. Input tersembunyi yang dikirim ke server (angka asli) --}}
                                <input type="hidden" 
                                    name="amount" 
                                    id="transaction_amount" 
                                    value="{{ old('amount') }}">

                                {{-- Error message tetap mengarah ke 'amount' --}}
                                @error('amount')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
        
                            {{-- Tanggal --}}
                            <div class="mb-3">
                                <label for="transaction_date" class="form-label">Tanggal</label>
                                <input type="date" class="form-control @error('date') is-invalid @enderror" id="transaction_date" name="date" required value="{{ old('date', date('Y-m-d')) }}">
                                @error('date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                    </div>
                    {{-- Akhir dari sistem grid --}}

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
    // 1. Ubah data dari PHP ke JSON yang bisa diakses JavaScript (Sertakan saved_amount)
    const EXPENSE_ALLOCATIONS = @json($expenseAllocations->map->only(['id', 'name', 'amount_allocated']));
    const SAVING_GOALS = @json($savingGoals->map->only(['id', 'goal_name', 'saved_amount']));

    document.addEventListener('DOMContentLoaded', function () {
        // --- Elemen Utama ---
        const addTransactionModal = document.getElementById('addTransactionModal');
        const transactionForm = document.getElementById('transactionForm');
        const typeSelect = document.getElementById('transaction_type');
        const namaTransaksiLabel = document.getElementById('namaTransaksiLabel');
        const dynamicNamaTransaksiContent = document.getElementById('dynamicNamaTransaksiContent');
        const submitButton = transactionForm.querySelector('button[type="submit"]');

        // --- Elemen Kategori ---
        const expensesCategoryGroup = document.getElementById('expensesCategoryGroup');
        const savingCategoryGroup = document.getElementById('savingCategoryGroup');
        const expenseCategorySelect = document.getElementById('expenses_category_id_general');
        const newCategoryInputGroup = document.getElementById('newCategoryInputGroup');
        const newCategoryNameInput = document.getElementById('new_category_name_input');
        const cancelNewCategoryBtn = document.getElementById('cancelNewCategory');

        // --- Elemen Jumlah, Saldo, & Peringatan ---
        const amountDisplay = document.getElementById('transaction_amount_display');
        const amountHidden = document.getElementById('transaction_amount');
        const savingGoalBalanceInfo = document.getElementById('savingGoalBalanceInfo'); // Dari HTML yang Anda tambahkan
        const balanceAmount = document.getElementById('balanceAmount'); // Dari HTML yang Anda tambahkan
        const amountWarning = document.getElementById('amountWarning'); // Pastikan ini juga ada di HTML

        // ===================================================================
        // LOGIKA FORMAT RUPIAH
        // ===================================================================
        amountDisplay.addEventListener('input', function(e) {
            let rawValue = e.target.value.replace(/[^0-9]/g, '');
            amountHidden.value = rawValue;
            e.target.value = rawValue ? new Intl.NumberFormat('id-ID').format(rawValue) : '';
            validateWithdrawalAmount(); // Validasi setiap kali jumlah berubah
        });

        // Saat halaman dimuat, jika ada nilai 'old', format itu
        if (amountHidden.value) {
            amountDisplay.value = new Intl.NumberFormat('id-ID').format(amountHidden.value);
        }

        // ===================================================================
        // FUNGSI INTI UNTUK SALDO & VALIDASI
        // ===================================================================

        // Fungsi untuk menampilkan Saldo Tersedia
        function showAvailableBalance() {
            const savingGoalSelect = document.getElementById('saving_goal_id');
            if (!savingGoalSelect || !savingGoalSelect.value || (typeSelect.value !== 'saving_deposit' && typeSelect.value !== 'saving_withdrawal')) {
                savingGoalBalanceInfo.style.display = 'none';
                return;
            }

            const goal = SAVING_GOALS.find(g => g.id == savingGoalSelect.value);
            if (goal) {
                balanceAmount.textContent = `Rp ${new Intl.NumberFormat('id-ID').format(goal.saved_amount)}`;
                savingGoalBalanceInfo.style.display = 'block';
            } else {
                savingGoalBalanceInfo.style.display = 'none';
            }
        }

        // Fungsi untuk validasi jumlah penarikan
        function validateWithdrawalAmount() {
            amountWarning.style.display = 'none';
            submitButton.disabled = false;
            
            if (typeSelect.value !== 'saving_withdrawal') return;

            const savingGoalSelect = document.getElementById('saving_goal_id');
            if (!savingGoalSelect || !savingGoalSelect.value) return;

            const withdrawalAmount = parseInt(amountHidden.value) || 0;
            const selectedGoal = SAVING_GOALS.find(goal => goal.id == savingGoalSelect.value);

            if (selectedGoal && withdrawalAmount > 0) {
                const availableAmount = parseInt(selectedGoal.saved_amount);
                if (withdrawalAmount > availableAmount) {
                    amountWarning.textContent = `Jumlah melebihi saldo tersedia (Rp ${new Intl.NumberFormat('id-ID').format(availableAmount)}).`;
                    amountWarning.style.display = 'block';
                    submitButton.disabled = true;
                }
            }
        }

        // ===================================================================
        // FUNGSI UTAMA FORM
        // ===================================================================

        function createOptions(data, valueField, textField, currentId) {
            return data.map(item => {
                const isSelected = item[valueField] == currentId ? 'selected' : '';
                let text = item[textField];
                if (item.amount_allocated) {
                     text += ` (Rp. ${new Intl.NumberFormat('id-ID').format(item.amount_allocated)})`;
                }
                return `<option value="${item[valueField]}" ${isSelected}>${text}</option>`;
            }).join('');
        }

        function renderNamaTransaksiFields(type, oldValues = {}) {
            let htmlContent = '';
            let currentDescription = oldValues.description || '';
            let currentExpenseAllocationId = oldValues.expense_allocation_id || '';
            let currentSavingGoalId = oldValues.saving_goal_id || '';
            let isUseAllocationChecked = oldValues.use_allocation === 'on';
            dynamicNamaTransaksiContent.innerHTML = '';

            if (type === 'income') {
                namaTransaksiLabel.textContent = 'Nama Transaksi';
                htmlContent = `<input type="text" class="form-control" id="description_text_input" name="description" placeholder="Ex: Gaji Bulanan" value="${currentDescription}" required>`;
            } else if (type === 'expense') {
                namaTransaksiLabel.textContent = 'Nama Transaksi';
                const allocationOptions = createOptions(EXPENSE_ALLOCATIONS, 'id', 'name', currentExpenseAllocationId);
                htmlContent = `
                    <div class="form-check mb-2"><input class="form-check-input" type="checkbox" id="useAllocationCheckbox" name="use_allocation" ${isUseAllocationChecked ? 'checked' : ''}><label class="form-check-label" for="useAllocationCheckbox">Pilih dari Alokasi Pengeluaran</label></div>
                    <div id="expenseDescriptionInputGroup" class="${isUseAllocationChecked ? 'd-none' : ''}"><input type="text" class="form-control" id="description_text_input" name="description" placeholder="Ex: Beli Kopi" value="${currentDescription}" ${!isUseAllocationChecked ? 'required' : ''} ${isUseAllocationChecked ? 'disabled' : ''}></div>
                    <div id="expenseAllocationDropdownGroup" class="mt-2 ${isUseAllocationChecked ? '' : 'd-none'}"><select class="form-select" id="expense_allocation_id" name="expense_allocation_id" ${isUseAllocationChecked ? 'required' : ''} ${!isUseAllocationChecked ? 'disabled' : ''}><option value="">-- Pilih Alokasi --</option>${allocationOptions}</select></div>`;
            } else if (type === 'saving_deposit' || type === 'saving_withdrawal') {
                namaTransaksiLabel.textContent = 'Pilih Tujuan Tabungan';
                const savingGoalOptions = createOptions(SAVING_GOALS, 'id', 'goal_name', currentSavingGoalId);
                htmlContent = `
                    <input type="hidden" name="use_saving_goal" value="on">
                    <div id="savingGoalDropdownGroup"><select class="form-select" id="saving_goal_id" name="saving_goal_id" required><option value="">-- Pilih Tujuan Tabungan --</option>${savingGoalOptions}</select></div>`;
            } else {
                 namaTransaksiLabel.textContent = 'Nama Transaksi';
                 htmlContent = `<input type="text" class="form-control" id="description_text_input" name="description" placeholder="Pilih jenis transaksi dahulu" value="" disabled>`;
            }
            dynamicNamaTransaksiContent.innerHTML = htmlContent;
            attachDynamicEventListeners();
        }
        
        function attachDynamicEventListeners() {
            const useAllocationCheckbox = document.getElementById('useAllocationCheckbox');
            if (useAllocationCheckbox) {
                useAllocationCheckbox.addEventListener('change', function() {
                    const isChecked = this.checked;
                    document.getElementById('expenseDescriptionInputGroup').classList.toggle('d-none', isChecked);
                    document.getElementById('expenseAllocationDropdownGroup').classList.toggle('d-none', !isChecked);
                    const descInput = document.getElementById('description_text_input');
                    const allocSelect = document.getElementById('expense_allocation_id');
                    descInput.disabled = isChecked;
                    allocSelect.disabled = !isChecked;
                    if (isChecked) {
                        descInput.removeAttribute('required'); allocSelect.setAttribute('required', 'true'); allocSelect.focus();
                    } else {
                        allocSelect.removeAttribute('required'); descInput.setAttribute('required', 'true'); descInput.focus();
                    }
                });
            }

            // Pasang event listener ke dropdown tujuan tabungan setiap kali dirender
            const savingGoalSelect = document.getElementById('saving_goal_id');
            if (savingGoalSelect) {
                savingGoalSelect.addEventListener('change', function() {
                    showAvailableBalance();
                    validateWithdrawalAmount();
                });
            }
        }
        
        function updateFormFieldsVisibility(oldValues = {}) {
            const selectedType = typeSelect.value;
            if (selectedType === 'expense') {
                expensesCategoryGroup.classList.remove('d-none'); savingCategoryGroup.classList.add('d-none');
                expenseCategorySelect.setAttribute('required', 'true'); expenseCategorySelect.setAttribute('name', 'expenses_category_id');
            } else if (selectedType === 'saving_deposit' || selectedType === 'saving_withdrawal') {
                expensesCategoryGroup.classList.add('d-none'); savingCategoryGroup.classList.remove('d-none');
                expenseCategorySelect.removeAttribute('required'); expenseCategorySelect.removeAttribute('name'); 
            } else {
                expensesCategoryGroup.classList.remove('d-none'); savingCategoryGroup.classList.add('d-none');
                expenseCategorySelect.removeAttribute('required'); expenseCategorySelect.setAttribute('name', 'expenses_category_id');
            }
            renderNamaTransaksiFields(selectedType, oldValues);
            showAvailableBalance(); // Panggil setelah render
            validateWithdrawalAmount(); // Panggil setelah render
        }

        function clearFormState() {
            transactionForm.reset();
            updateFormFieldsVisibility();
            amountDisplay.value = '';
            amountHidden.value = '';
            savingGoalBalanceInfo.style.display = 'none'; // Sembunyikan info saldo
            amountWarning.style.display = 'none'; // Sembunyikan peringatan
            submitButton.disabled = false; // Aktifkan kembali tombol simpan
            newCategoryInputGroup.classList.add('d-none');
            expenseCategorySelect.classList.remove('d-none');
            newCategoryNameInput.removeAttribute('name');
            document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
            document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
             const errorAlert = addTransactionModal.querySelector('.alert-danger');
             if (errorAlert) errorAlert.classList.add('d-none');
        }

        // --- EVENT LISTENERS UTAMA ---
        typeSelect.addEventListener('change', () => updateFormFieldsVisibility());

        expenseCategorySelect.addEventListener('change', function() {
            if (this.value === 'add_new_category') {
                expenseCategorySelect.classList.add('d-none'); newCategoryInputGroup.classList.remove('d-none');
                newCategoryNameInput.setAttribute('name', 'new_category_name'); newCategoryNameInput.setAttribute('required', 'true');
                newCategoryNameInput.focus(); expenseCategorySelect.removeAttribute('required');
            }
        });

        cancelNewCategoryBtn.addEventListener('click', function() {
            newCategoryInputGroup.classList.add('d-none'); newCategoryNameInput.removeAttribute('name');
            newCategoryNameInput.removeAttribute('required'); newCategoryNameInput.value = '';
            expenseCategorySelect.classList.remove('d-none'); expenseCategorySelect.value = '';
            if (typeSelect.value === 'expense') {
                expenseCategorySelect.setAttribute('required', 'true');
            }
        });
        
        // Logika untuk menampilkan modal jika ada error
        @if ($errors->any())
            const oldValues = {
                type: "{{ old('type') }}",
                description: "{{ old('description') }}",
                expense_allocation_id: "{{ old('expense_allocation_id') }}",
                saving_goal_id: "{{ old('saving_goal_id') }}",
                use_allocation: "{{ old('use_allocation') }}",
                use_saving_goal: "{{ old('use_saving_goal') }}",
                expenses_category_id: "{{ old('expenses_category_id') }}",
                new_category_name: "{{ old('new_category_name') }}",
                amount: "{{ old('amount') }}",
                date: "{{ old('date') }}",
            };
            const myModal = new bootstrap.Modal(addTransactionModal);
            myModal.show();
            typeSelect.value = oldValues.type;
            updateFormFieldsVisibility(oldValues);
            
            if (oldValues.new_category_name) {
                expenseCategorySelect.value = 'add_new_category';
                expenseCategorySelect.dispatchEvent(new Event('change'));
                newCategoryNameInput.value = oldValues.new_category_name;
            } else {
                 expenseCategorySelect.value = oldValues.expenses_category_id;
            }
        @endif
        
        addTransactionModal.addEventListener('hidden.bs.modal', () => clearFormState());
        updateFormFieldsVisibility(); // Inisialisasi form saat pertama kali dimuat
    });
</script>
@endpush