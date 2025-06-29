@extends('layouts.app')

@section('title', 'Tabungan Diarsipkan - Dompet Pintar')

@push('styles')
<style>
    :root {
        --green-light: #d9f99d;
    }

    .container-fluid {
        padding: 1rem;
    }

    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .dashboard-header h1 {
        font-size: 1.5rem;
        font-weight: 600;
        margin: 0;
        color: #111827;
    }

    .header-actions {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .header-actions .bi-bell-fill {
        font-size: 1.2rem;
        color: #9CA3AF;
    }

    .user-profile img {
        width: 32px;
        height: 32px;
        object-fit: cover;
        border-radius: 9999px;
    }

    .action-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .search-form {
        position: relative;
        flex-grow: 1;
        max-width: 320px;
        width: 100%;
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
        flex-wrap: wrap;
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

    .btn-filter {
        background-color: #F8FAFC;
        border: 1px solid #E2E8F0;
        color: #1F2937;
    }

    .btn-tambah {
        background-color: var(--green-light, #A3D959);
        color: #1F2937;
    }

    .btn-tambah:hover {
        background-color: #c6e659;
    }

    .saving-goals-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 0.75rem;
        
    }

    .saving-goals-table thead th {
        text-transform: uppercase;
        color: #9CA3AF;
        font-size: 0.7rem;
        font-weight: 500;
        padding: 0 1rem 0.5rem 1rem;
        text-align: left;
        white-space: nowrap;
    }

    .saving-goals-table tbody tr {
        background-color: #fff;
        border-radius: 0.75rem;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        transition: box-shadow 0.2s ease-in-out;
    }

    .saving-goals-table tbody tr:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }

    .saving-goals-table tbody td {
        padding: 1.25rem 1rem;
        vertical-align: middle;
        font-size: 0.875rem;
        white-space: nowrap;
        border: none;
    }

    .saving-goals-table tbody td:first-child {
        border-top-left-radius: 0.75rem;
        border-bottom-left-radius: 0.75rem;
    }

    .saving-goals-table tbody td:last-child {
        border-top-right-radius: 0.75rem;
        border-bottom-right-radius: 0.75rem;
        text-align: right;
    }

    .progress-container .progress-info {
        display: flex;
        gap: 0.5rem;
        align-items: baseline;
        margin-bottom: 0.5rem;
    }

    .progress-container .percentage {
        font-weight: 700;
        font-size: 0.9rem;
    }

    .progress-container .status {
        color: #9CA3AF;
        font-size: 0.75rem;
    }

    .progress {
        height: 8px;
        border-radius: 8px;
        background-color: #F3F4F6;
    }

    .progress-bar {
        background-color: #16A34A;
    }

    .action-cell {
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
    }

    .btn-detail {
        background-color: #FEF9C3;
        border: 1px solid #FDE68A;
        color: #CA8A04;
        border-radius: 0.5rem;
        font-size: 0.8rem;
        font-weight: 500;
        padding: 0.4rem 0.8rem;
    }

    .btn-detail:hover {
        background-color: #FDE68A;
        border-color: #FACC15;
    }

    @media (max-width: 768px) {
        .dashboard-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .header-actions {
            width: 100%;
            justify-content: space-between;
        }

        .saving-goals-table thead {
            display: none;
        }

        .saving-goals-table tbody tr {
            display: block;
            width: 100%;
            margin-bottom: 1rem;
        }

        .saving-goals-table tbody td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e2e8f0;
            font-size: 0.875rem;
        }

        .saving-goals-table tbody td::before {
            content: attr(data-label);
            font-weight: 600;
            color: #6b7280;
        }

        .saving-goals-table tbody td:last-child {
            border-bottom: none;
            justify-content: flex-end;
        }

        .progress-container .progress-info {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    @php $user = auth()->user(); @endphp

    <header class="dashboard-header">
        <h1>Archived</h1>
        <div class="header-actions">
            <i class="bi bi-bell-fill"></i>
            <div class="user-profile dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none text-dark" data-bs-toggle="dropdown">
                    <img src="{{ $user->avatar ?? 'https://i.pravatar.cc/40?u=' . $user->id }}" alt="User Avatar">
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
            <input type="text" class="form-control" placeholder="Cari Tabungan">
        </form>
        <div class="action-buttons">
            <button class="btn btn-filter">
                <i class="bi bi-funnel"></i> Filters
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="table-responsive">
        <table class="saving-goals-table table">
            <thead>
                <tr>
                    <th>Nama Tabungan</th>
                    <th>Deadline</th>
                    <th>Target</th>
                    <th>Tabungan Saat Ini</th>
                    <th style="width: 20%;">Progres</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($archivedGoals as $goal)
                    @php
                        $progress = $goal->target_amount > 0 ? ($goal->saved_amount / $goal->target_amount) * 100 : 0;
                    @endphp
                    <tr>
                        <td class="fw-bold" data-label="Nama Tabungan">{{ $goal->goal_name }}</td>
                        <td data-label="Deadline">{{ \Carbon\Carbon::parse($goal->deadline)->setTimezone('Asia/Jakarta')->format('d M Y') }}</td>
                        <td data-label="Target">Rp. {{ number_format($goal->target_amount, 0, ',', '.') }}</td>
                        <td class="fw-bold" data-label="Tabungan Saat Ini">Rp. {{ number_format($goal->saved_amount, 0, ',', '.') }}</td>
                        <td data-label="Progres">
                            <div class="progress-container">
                                <div class="progress-info">
                                    <span class="percentage">{{ round($progress) }}%</span>
                                    <span class="status">{{ $progress >= 100 ? 'Tercapai' : 'Belum Tercapai' }}</span>
                                </div>
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $progress }}%;" aria-valuenow="{{ $progress }}"></div>
                                </div>
                            </div>
                        </td>
                        <td class="action-cell" data-label="Action">
                            <a href="#" class="btn btn-detail" data-bs-toggle="modal" data-bs-target="#detailGoalModal{{ $goal->id }}">Detail</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 bg-white" style="border-radius: 0.75rem;">
                            <p class="mb-0 text-muted">Tidak ada tabungan yang diarsipkan.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @include('components.modal-detail-goal', ['activeGoals' => $archivedGoals])
</div>
@endsection
