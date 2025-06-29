@extends('layouts.app')

@section('title', 'Transaksi - Dompet Pintar')
@include('components.modal-transaction')
@include('components.modal-detail-transaction')

@push('styles')
<style>
    .action-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1.5rem;
        margin-bottom: 1.5rem;
        gap: 1rem;
    }

    .search-form {
        position: relative;
        flex-grow: 1;
        max-width: 320px;
    }

    .search-form .bi-search {
        position: absolute;
        top: 50%;
        left: 1rem;
        transform: translateY(-50%);
        color: #9CA3AF;
    }

    .search-form .form-control {
        border-radius: 0.75rem;
        padding-left: 2.75rem;
        background-color: #F8FAFC;
        border: 1px solid #E2E8F0;
        height: 44px;
        font-size: 0.875rem;
    }

    .action-buttons {
        display: flex;
        gap: 0.75rem;
    }

    .action-buttons .btn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        border-radius: 0.75rem;
        font-weight: 600;
        padding: 0.6rem 1rem;
        font-size: 0.875rem;
        border: none;
    }

    .btn-tambah {
        background-color: var(--green-light, #A3D959);
        color: #1F2937;
    }

    .btn-tambah:hover {
        background-color: #c6e659;
    }

    .btn-filter {
        background-color: #F8FAFC;
        border: 1px solid #E2E8F0;
        color: #1F2937;
    }

    .btn-filter:hover {
        background-color: #F1F5F9;
        border-color: #CBD5E1;
    }

    /* Tabel transaksi responsive container */
    .table-responsive-custom {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .transactions-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 0.75rem;
        min-width: 700px;
    }

    .transactions-table thead th {
        text-transform: uppercase;
        color: #9CA3AF;
        font-size: 0.7rem;
        font-weight: 500;
        padding: 0 1rem 0.5rem 1rem;
        text-align: left;
        white-space: nowrap;
    }

    .transactions-table tbody tr {
        background-color: #fff;
        border-radius: 0.75rem;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        transition: box-shadow 0.2s ease-in-out;
    }

    .transactions-table tbody td {
        padding: 1rem;
        vertical-align: middle;
        font-size: 0.875rem;
        border: none;
        white-space: nowrap;
    }

    .transactions-table tbody td:first-child {
        border-top-left-radius: 0.75rem;
        border-bottom-left-radius: 0.75rem;
        font-weight: 600;
    }

    .transactions-table tbody td:last-child {
        border-top-right-radius: 0.75rem;
        border-bottom-right-radius: 0.75rem;
        text-align: right;
    }

    .amount-cell.pemasukan { color: #16A34A; }
    .amount-cell.pengeluaran { color: #DC2626; }
    .amount-cell.tabungan { color: #3B82F6; }
    .amount-cell { font-weight: 600; }

    .btn-view {
        background-color: #FEF9C3;
        border: 1px solid #FDE68A;
        color: #CA8A04;
        border-radius: 0.5rem;
        font-size: 0.8rem;
        font-weight: 500;
        padding: 0.4rem 1rem;
    }

    .btn-view:hover {
        background-color: #FDF2A3;
    }

    @media (max-width: 768px) {
        .action-bar {
            flex-direction: column;
            align-items: stretch;
        }

        .search-form {
            max-width: 100%;
            width: 100%;
        }

        .action-buttons {
            flex-direction: column;
            width: 100%;
        }

        .action-buttons .btn {
            width: 100%;
            justify-content: center;
        }
        .transactions-table {
        min-width: unset;
        border-spacing: 0;
    }

    .transactions-table thead {
        display: none;
    }

    .transactions-table tbody tr {
        display: block;
        background-color: #fff;
        margin-bottom: 1rem;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 1rem;
    }

    .transactions-table tbody td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        font-size: 0.875rem;
        border: none;
        white-space: normal;
    }

    .transactions-table tbody td::before {
        content: attr(data-label);
        font-weight: 600;
        color: #6B7280;
        flex-shrink: 0;
    }

    .transactions-table tbody td span {
        text-align: right;
        flex: 1;
        margin-left: 1rem;
        overflow-wrap: anywhere;
    }

    .transactions-table tbody td:last-child {
        justify-content: flex-end;
    }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    @php $user = auth()->user(); @endphp

    <header class="dashboard-header d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <h1>Transaksi</h1>
        <div class="header-actions d-flex align-items-center gap-3">
            <i class="bi bi-bell-fill"></i>
            <div class="user-profile dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none text-dark" data-bs-toggle="dropdown">
                    <img src="{{ $user->avatar ?? asset('assets/default-avatar.png') }}" alt="User Avatar" width="32" height="32" class="rounded-circle object-fit-cover">
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

    <div class="action-bar">
        <form class="search-form">
            <i class="bi bi-search"></i>
            <input type="text" class="form-control" placeholder="Cari Transaksi">
        </form>
        <div class="action-buttons">
            <button type="button" class="btn btn-tambah" data-bs-toggle="modal" data-bs-target="#addTransactionModal">
                <i class="bi bi-wallet-fill"></i> Tambah Transaksi
            </button>
            <button class="btn btn-filter">
                <i class="bi bi-funnel"></i> Filters
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive-custom">
        <table class="transactions-table saving-goals-table">
            <thead>
                <tr>
                    <th>Nama Transaksi</th>
                    <th>Jenis</th>
                    <th>Kategori</th>
                    <th>Jumlah</th>
                    <th>Tanggal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($transactions as $transaction)
                    @php
                        $type = strtolower($transaction->type);
                        $amountClass = match($type) {
                            'income' => 'pemasukan',
                            'expense' => 'pengeluaran',
                            'saving_deposit' => 'tabungan',
                            default => ''
                        };
                    @endphp
                    <tr>
                        <td data-label="Nama Transaksi">{{ $transaction->description }}</td>
                    <td data-label="Jenis">{{ $transaction->type }}</td>
                    <td data-label="Kategori">{{ $transaction->expenseCategory->name ?? ($transaction->savingGoal ? 'Tabungan' : '-') }}</td>
                    <td data-label="Jumlah" class="amount-cell {{ $amountClass }}">
                        Rp. {{ number_format($transaction->amount, 0, ',', '.') }}
                    </td>
                    <td data-label="Tanggal" class="date-cell">
                        {{ \Carbon\Carbon::parse($transaction->date)->format('d F Y') }}
                        <small>at {{ \Carbon\Carbon::parse($transaction->date)->format('h:i A') }}</small>
                    </td>
                    <td data-label="Action" class="action-cell">
                            <button type="button" class="btn btn-view"
                                data-bs-toggle="modal"
                                data-bs-target="#detailTransactionModal"
                                data-description="{{ $transaction->description }}"
                                data-amount="{{ number_format($transaction->amount, 0, ',', '.') }}"
                                data-type="{{ ucwords(str_replace('_', ' ', $transaction->type)) }}"
                                data-date="{{ \Carbon\Carbon::parse($transaction->date)->isoFormat('D MMMM YYYY') }}"
                                data-category="{{ $transaction->expenseCategory->name ?? '-' }}"
                                data-amount-modal-class="text-{{ $amountClass == 'pemasukan' ? 'success' : ($amountClass == 'pengeluaran' ? 'danger' : ($amountClass == 'tabungan' ? 'primary' : 'dark')) }}">
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
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const detailModal = document.getElementById('detailTransactionModal');
    if (detailModal) {
        detailModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const description = button.getAttribute('data-description');
            const amount = button.getAttribute('data-amount');
            const type = button.getAttribute('data-type');
            const date = button.getAttribute('data-date');
            const category = button.getAttribute('data-category');
            const amountClass = button.getAttribute('data-amount-modal-class');

            detailModal.querySelector('.modal-title').textContent = 'Detail Transaksi';
            detailModal.querySelector('#detail-description').textContent = description;
            const amountElement = detailModal.querySelector('#detail-amount');
            amountElement.textContent = 'Rp. ' + amount;
            amountElement.className = 'fw-bold ' + (amountClass || '');
            detailModal.querySelector('#detail-type').textContent = type;
            detailModal.querySelector('#detail-date').textContent = date;
            detailModal.querySelector('#detail-category').textContent = category;
        });
    }
});
</script>
@endpush

