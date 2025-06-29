<style>
    /* Mengatur ukuran font untuk judul modal */
    #allocationModal .modal-title {
        font-size: 1rem; /* Ukuran yang lebih besar dari default h5 */
        font-weight: 600; /* Sedikit lebih tebal */
    }

    /* Mengatur ukuran font untuk label form di dalam modal */
    #allocationModal .form-label {
        font-size: 1rem; /* Sedikit lebih besar dari teks biasa */
    }

    /* Mengatur ukuran font untuk teks 'Sisa Dana Tabungan Bulan Ini' */
    #allocationModal #remainingDisplay {
        font-size: 1.5rem; /* Cukup besar agar mudah terlihat */
        font-weight: bold;
        color: #28a745; /* Contoh warna hijau Bootstrap */
    }

    /* Mengatur ukuran font untuk teks small di dalam label tujuan */
    #allocationModal label small {
        font-size: 0.85em; /* Tetap kecil tapi mudah dibaca */
    }

    /* Mengatur ukuran font untuk input form */
    #allocationModal .form-control {
        font-size: 1rem; /* Ukuran standar */
    }
    
    #allocationModal .alert {
            padding: 0.5rem 0.75rem; /* Padding atas/bawah 0.5rem, kiri/kanan 0.75rem */
            font-size: 0.7rem;     /* Ukuran font sedikit lebih kecil */
            margin-bottom: 0.75rem;  /* Jarak bawah dikurangi */
        }

        /* Aturan khusus untuk alert peringatan per goal jika berbeda */
        #allocationModal .alert-warning {
             /* Anda bisa tambahkan aturan spesifik lagi di sini jika ingin warning per goal lebih kecil lagi */
             /* Contoh: font-size: 0.8rem; padding: 0.4rem 0.6rem; */
        }

        #allocationModal .modal-footer {
            justify-content: space-between; /* Ini akan mendorong item ke ujung kiri dan kanan */
        }
</style>
<div class="modal fade" id="allocationModal" tabindex="-1" aria-labelledby="allocationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="allocationModalLabel">
                    Alokasi Tabungan - Bulan {{ $allocation ? \Carbon\Carbon::parse($allocation->tanggal)->format('F Y') : 'Tidak Tersedia' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div>
                    @if ($allocation)
                         <form action="{{ route('goal-allocations.store') }}" method="POST" id="allocation-form">
                            @csrf
                            <input type="hidden" name="income_allocation_id" value="{{ $allocation->id }}">

                            <div class="mb-3">
                                <label class="form-label fw-bold">Sisa Dana Tabungan Bulan Ini:</label>
                                <p class="form-control-plaintext h5" id="remainingDisplay">Rp {{ number_format($tabunganTotal, 0, ',', '.') }} </p>
                                <input type="hidden" id="totalAvailable" value="{{ $tabunganTotal }}">
                            </div>

                            @foreach ($goals as $goal)
                                @php
                                    $defaultValue = $existingAllocations[$goal->id] ?? 0;
                                    $remaining = $goal->target_amount - $goal->saved_amount;
                                    $monthsLeft = \Carbon\Carbon::now()->startOfMonth()->diffInMonths(\Carbon\Carbon::parse($goal->deadline)->startOfMonth());
                                    $perMonthNeed = $monthsLeft > 0 ? ceil($remaining / $monthsLeft) : $remaining;
                                @endphp
                                <div class="mb-3">
                                    <label class="form-label">
                                        {{ $goal->goal_name }}
                                        <small class="text-muted">
                                            (Target: Rp {{ number_format($goal->target_amount, 0, ',', '.') }},
                                            Deadline: {{ \Carbon\Carbon::parse($goal->deadline)->format('d M Y') }},
                                            Sisa: Rp {{ number_format($remaining, 0, ',', '.') }},
                                            Butuh/Bulan: <strong class="text-danger">Rp {{ number_format($perMonthNeed, 0, ',', '.') }}</strong>)
                                        </small>
                                    </label>
                                    <input
                                        type="text"
                                        class="form-control allocation-input formatted-input"
                                        data-goal-id="{{ $goal->id }}"
                                        data-per-month="{{ $perMonthNeed }}"
                                        data-hidden-id="hidden-{{ $goal->id }}"
                                        value="Rp {{ number_format($defaultValue, 0, ',', '.') }}"
                                        placeholder="Contoh: Rp {{ number_format($perMonthNeed, 0, ',', '.') }}"
                                    >
                                    <input
                                        type="hidden"
                                        name="allocations[{{ $goal->id }}]"
                                        id="hidden-{{ $goal->id }}"
                                        value="{{ $defaultValue }}"
                                    >
                                    <div class="alert alert-warning py-2 px-3 mt-2 d-none" id="warning-{{ $goal->id }}">
                                        ⚠️ Alokasi lebih kecil dari yang dibutuhkan per bulan. Perpanjang deadline atau tambah dana.
                                    </div>
                                </div>
                            @endforeach

                            <div id="warning" class="alert alert-danger d-none" role="alert">
                                ⚠️ **Total alokasi melebihi batas tabungan!** Kurangi jumlah alokasi agar sesuai dana tersedia.
                            </div>

                        </form>
                    @else
                        <div class="alert alert-info">Belum ada data alokasi pemasukan untuk bulan ini. Harap tambahkan terlebih dahulu.</div>
                    @endif
                </div>
            </div>
            <div class="modal-footer">
                {{-- PERUBAHAN DI SINI: --}}
                {{-- Tombol Simpan di pojok kiri, tombol Tutup di pojok kanan --}}
                <button type="button" class="btn btn-primary me-auto" id="submitBtn">Simpan Alokasi</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        var allocationModalElement = document.getElementById('allocationModal');
        if (!allocationModalElement) {
            console.warn("Modal with ID 'allocationModal' not found.");
            return;
        }

        const inputs = allocationModalElement.querySelectorAll('.formatted-input');
        const allocationInputs = allocationModalElement.querySelectorAll('.allocation-input');
        const remainingDisplay = allocationModalElement.querySelector('#remainingDisplay');
        const totalAvailableInput = allocationModalElement.querySelector('#totalAvailable');
        const warning = allocationModalElement.querySelector('#warning');
        const submitButton = allocationModalElement.querySelector('#submitBtn');
        const allocationForm = allocationModalElement.querySelector('#allocation-form');

        const totalAvailable = totalAvailableInput ? parseInt(totalAvailableInput.value) : 0;
        if (totalAvailableInput === null) {
            console.warn("Input with ID 'totalAvailable' not found inside the modal.");
        }

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

        function updateRemaining() {
            let totalUsed = 0;
            let valid = true;

            allocationInputs.forEach(input => {
                const goalId = input.dataset.goalId;
                const perMonthNeed = parseInt(input.dataset.perMonth);
                const hiddenId = input.dataset.hiddenId;
                const hiddenInput = document.getElementById(hiddenId);

                const val = unformatRupiah(input.value);
                if (hiddenInput) {
                    hiddenInput.value = val;
                }
                totalUsed += val;

                const warningPerGoal = document.getElementById('warning-' + goalId);
                if (warningPerGoal) {
                    if (val < perMonthNeed) {
                        warningPerGoal.classList.remove('d-none');
                        valid = false;
                    } else {
                        warningPerGoal.classList.add('d-none');
                    }
                }
            });

            if (remainingDisplay) {
                const remaining = totalAvailable - totalUsed;
                remainingDisplay.innerText = formatRupiah(remaining);

                if (remaining < 0) {
                    if (warning) warning.classList.remove('d-none');
                    valid = false;
                } else {
                    if (warning) warning.classList.add('d-none');
                }
            }

            if (submitButton) {
                submitButton.disabled = !valid;
            }
        }

        inputs.forEach(input => {
            input.addEventListener('input', function () {
                const value = unformatRupiah(this.value);
                this.value = formatRupiah(value);
                updateRemaining();
            });
        });

        if (submitButton && allocationForm) {
            submitButton.addEventListener('click', function(e) {
                allocationForm.submit();
            });
        }

        allocationModalElement.addEventListener('shown.bs.modal', function () {
            updateRemaining();
        });

        updateRemaining();
    });
</script>
@endpush