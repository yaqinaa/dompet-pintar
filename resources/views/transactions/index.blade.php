@extends('layouts.app')

@section('title', 'Transaksi - Dompet Pintar')
@include('components.modal-transaction')
@include('components.modal-detail-transaction')
@push('styles')
<style>
    /* ... (CSS Anda dari .action-bar sampai .amount-cell tetap sama) ... */
    .action-bar { display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem; margin-bottom: 1.5rem; gap: 1rem; }
    .search-form { position: relative; flex-grow: 1; max-width: 320px; }
    .search-form .bi-search { position: absolute; top: 50%; left: 1rem; transform: translateY(-50%); color: #9CA3AF; }
    .search-form .form-control { border-radius: 0.75rem; padding-left: 2.75rem; background-color: #F8FAFC; border: 1px solid #E2E8F0; height: 44px; font-size: 0.875rem; }
    .action-buttons { display: flex; gap: 0.75rem; }
    .action-buttons .btn { display: flex; align-items: center; gap: 0.5rem; border-radius: 0.75rem; font-weight: 600; padding: 0.6rem 1rem; font-size: 0.875rem; border: none; transition: background-color 0.2s ease-in-out; } /* Tambah transisi */
    .btn-tambah { background-color: var(--green-light, #A3D959); color: #1F2937; }
    .btn-filter { background-color: #F8FAFC; border: 1px solid #E2E8F0; color: #1F2937; }
    .transactions-table { width: 100%; border-collapse: separate; border-spacing: 0 0.75rem; }
    .transactions-table thead th { text-transform: uppercase; color: #9CA3AF; font-size: 0.7rem; font-weight: 500; padding: 0 1rem 0.5rem 1rem; text-align: left; }
    .transactions-table tbody tr { background-color: #fff; border-radius: 0.75rem; box-shadow: 0 1px 2px rgba(0,0,0,0.03); transition: box-shadow 0.2s ease-in-out; }
    .transactions-table tbody tr:hover { box-shadow: 0 4px 8px rgba(0,0,0,0.05); }
    .transactions-table tbody td { padding: 1rem; vertical-align: middle; font-size: 0.875rem; border: none; }
    .transactions-table tbody td:first-child { border-top-left-radius: 0.75rem; border-bottom-left-radius: 0.75rem; font-weight: 600; }
    .transactions-table tbody td:last-child { border-top-right-radius: 0.75rem; border-bottom-right-radius: 0.75rem; text-align: right; }
    .date-cell small { display: block; color: #9CA3AF; font-size: 0.75rem; }
    .amount-cell.pemasukan { color: #16A34A; }
    .amount-cell.pengeluaran { color: #DC2626; }
    .amount-cell.tabungan { color: #3B82F6; }
    .amount-cell { font-weight: 600; }
    .action-cell { text-align: right; }
    .action-cell .btn { border-radius: 0.5rem; font-size: 0.8rem; font-weight: 500; padding: 0.4rem 1rem; transition: background-color 0.2s ease-in-out; } /* Tambah transisi */
    
    /* ========================================================== */
    /* PERBAIKAN DAN TAMBAHAN DI SINI */
    /* ========================================================== */

    /* 1. Atur warna tombol View sesuai permintaan */
    .btn-view {
        background-color: #FEF9C3; /* Kuning sangat muda */
        border: 1px solid #FDE68A;
        color: #CA8A04; /* Kuning tua */
    }

    /* 2. Atur efek hover untuk tombol Tambah */
    .btn-tambah:hover {
        background-color: #92C24F; /* Warna hijau sedikit lebih gelap */
    }

    /* 3. Atur efek hover untuk tombol View */
    .btn-view:hover {
        background-color: #FDF2A3; /* Warna kuning sedikit lebih gelap */
    }

    /* 4. (Opsional) Atur efek hover untuk tombol Filter */
    .btn-filter:hover {
        background-color: #F1F5F9;
    }

</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    @php
        // Mengambil data user yang sedang login
        $user = auth()->user();
    @endphp

    {{-- Header Halaman --}}
    <header class="dashboard-header">
        <h1>Transaksi</h1>
        <div class="header-actions">
           
            <i class="bi bi-bell-fill"></i>
            <div class="user-profile dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none text-dark" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="{{ $user->avatar ?? asset('assets/default-avatar.png') }}" alt="User Avatar">
                    <span class="d-none d-sm-inline mx-2">{{ $user->name }}</span>
                    <i class="bi bi-chevron-down"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#">Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#">Logout</a></li>
                </ul>
            </div>
        </div>
    </header>

    {{-- Action Bar: Search, Tambah, Filter --}}
    <div class="action-bar">
        <form class="search-form">
            <i class="bi bi-search"></i>
            <input type="text" class="form-control" placeholder="Search anything on Transactions">
        </form>
        <div class="action-buttons">
            {{-- Menggunakan route helper untuk tombol tambah --}}
            <button type="button" class="btn btn-tambah" data-bs-toggle="modal" data-bs-target="#addTransactionModal">
                <i class="bi bi-plus-lg"></i> Tambah Transaksi
            </button>
            <button class="btn btn-filter">
                <i class="bi bi-funnel"></i> Filters
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Tabel Transaksi --}}
    <table class="transactions-table">
        <thead>
            <tr>
                <th>Nama Transaksi</th>
                <th>Jenis</th>
                <th>Kategori</th>
                <th>Jumlah</th>
                <th>Tanggal</th>
                <th style="text-align: right;">Action</th>
            </tr>
        </thead>
        <tbody>
    @forelse ($transactions as $transaction)
        <tr>
            {{-- Menggunakan nama kolom dari Model Anda --}}
            <td>{{ $transaction['description'] }}</td>
            <td>{{ $transaction['type'] }}</td>
            
            <td>
                @if ($transaction->expenseCategory)
                    {{-- Jika ada relasi expenseCategory, tampilkan namanya --}}
                    {{-- Asumsi kolom nama di tabel expense_categories adalah 'name' --}}
                    {{ $transaction->expenseCategory->name }} 
                    
                @elseif ($transaction->savingGoal)
                    {{-- Jika ini transaksi tabungan, tampilkan "Tabungan" atau nama goal --}}
                    Tabungan
                @else
                    {{-- Jika tidak ada relasi (misal: Pemasukan), tampilkan jenisnya --}}
                    {{ $transaction->expenses_category_id }}
                @endif
            </td>

            <td class="amount-cell 
                {{ strtolower($transaction['type']) == 'income' ? 'pemasukan' : '' }}
                {{ strtolower($transaction['type']) == 'expense' ? 'pengeluaran' : '' }}
                {{-- Sesuaikan dengan nilai 'type' Anda --}}
                {{ strtolower($transaction['type']) == 'saving_deposit' ? 'tabungan' : '' }}">
                Rp. {{ number_format($transaction['amount'], 0, ',', '.') }}
            </td>

            <td class="date-cell">
                {{ \Carbon\Carbon::parse($transaction['date'])->format('d F Y') }}
                <small>at {{ \Carbon\Carbon::parse($transaction['date'])->format('h:i A') }}</small>
            </td>

            <td class="action-cell">
                <button type="button" class="btn btn-view" 
                data-bs-toggle="modal" 
                data-bs-target="#detailTransactionModal"
                data-description="{{ $transaction->description }}"
                data-amount="{{ number_format($transaction->amount, 0, ',', '.') }}"
                data-type="{{ ucwords(str_replace('_', ' ', $transaction->type)) }}"
                data-date="{{ \Carbon\Carbon::parse($transaction->date)->isoFormat('D MMMM YYYY') }}"
                data-category="{{ $transaction->expenseCategory->name ?? '-' }}"
                @php
                    if ($transaction->type == 'income') {
                        $modalAmountClass = 'text-success'; // Hijau
                    } elseif ($transaction->type == 'expense') {
                        $modalAmountClass = 'text-danger'; // Merah
                    } elseif (in_array($transaction->type, ['saving_deposit', 'saving_withdrawal'])) {
                        // Untuk tabungan, kita bisa gunakan warna biru, atau bedakan
                        if ($transaction->type == 'saving_deposit') {
                            $modalAmountClass = 'text-primary'; // Biru untuk setor
                        } else {
                            $modalAmountClass = 'text-dark'; // Hitam untuk tarik
                        }
                    }
                @endphp
                data-amount-modal-class="{{ $modalAmountClass }}"
                >
                View
            </button>
            </td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="text-center py-5 bg-white" style="border-radius: 0.75rem;">
                <p class="mb-0 text-muted">Belum ada transaksi.</p>
            </td>
        </tr>
    @endforelse
</tbody>
    </table>
</div>
@endsection
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- LOGIKA UNTUK MODAL DETAIL ---
        const detailModal = document.getElementById('detailTransactionModal');
    if (detailModal) {
        detailModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;

            // Ekstrak data
            const description = button.getAttribute('data-description');
            const amount = button.getAttribute('data-amount');
            const type = button.getAttribute('data-type');
            const date = button.getAttribute('data-date');
            const category = button.getAttribute('data-category');
            
            // Ambil class warna yang baru kita buat
            const amountClass = button.getAttribute('data-amount-modal-class');

            // Dapatkan elemen modal
            const modalTitle = detailModal.querySelector('.modal-title');
            const detailDescription = detailModal.querySelector('#detail-description');
            const detailAmount = detailModal.querySelector('#detail-amount');
            const detailType = detailModal.querySelector('#detail-type');
            const detailDate = detailModal.querySelector('#detail-date');
            const detailCategory = detailModal.querySelector('#detail-category');

            // Isi konten modal
            modalTitle.textContent = 'Detail Transaksi';
            detailDescription.textContent = description;
            detailAmount.textContent = 'Rp. ' + amount;
            detailType.textContent = type;
            detailDate.textContent = date;
            detailCategory.textContent = category;
            
            // =============================================
            // PERBAIKAN LOGIKA WARNA NOMINAL
            // =============================================
            // Reset class yang ada, sisakan hanya yang dasar
            detailAmount.className = 'fw-bold'; 
            
            // Tambahkan class warna yang sesuai dari data-attribute
            if (amountClass) {
                detailAmount.classList.add(amountClass); 
            }
        });
    }
    });
</script>
@endpush