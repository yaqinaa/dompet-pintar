@extends('layouts.app')

@section('title', 'Tabungan Diarsipkan - Dompet Pintar')

{{-- Kita bisa menggunakan kembali CSS dari halaman index tabungan. --}}
{{-- Jika Anda menaruh CSS di file terpisah, panggil di sini. Jika tidak, salin dari halaman index. --}}
@push('styles')
<style>
    /* SALIN SEMUA CSS DARI HALAMAN saving-goals/index.blade.php KE SINI */
    /* Ini untuk memastikan tampilannya konsisten */
    .action-bar { display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem; margin-bottom: 1.5rem; gap: 1rem; }
    .search-form { position: relative; flex-grow: 1; max-width: 320px; }
    .search-form .bi-search { position: absolute; top: 50%; left: 1rem; transform: translateY(-50%); color: #9CA3AF; }
    .search-form .form-control { border-radius: 0.75rem; padding-left: 2.75rem; background-color: #F8FAFC; border: 1px solid #E2E8F0; height: 44px; font-size: 0.875rem; }
    .action-buttons { display: flex; gap: 0.75rem; }
    .action-buttons .btn { display: flex; align-items: center; gap: 0.5rem; border-radius: 0.75rem; font-weight: 600; padding: 0.6rem 1rem; font-size: 0.875rem; border: none; }
    .btn-filter { background-color: #F8FAFC; border: 1px solid #E2E8F0; color: #1F2937; }
    
    .saving-goals-table { width: 100%; border-collapse: separate; border-spacing: 0 0.75rem; }
    .saving-goals-table thead th { text-transform: uppercase; color: #9CA3AF; font-size: 0.7rem; font-weight: 500; padding: 0 1rem 0.5rem 1rem; text-align: left; }
    .saving-goals-table tbody tr { background-color: #fff; border-radius: 0.75rem; box-shadow: 0 1px 2px rgba(0,0,0,0.03); transition: box-shadow 0.2s ease-in-out; }
    .saving-goals-table tbody tr:hover { box-shadow: 0 4px 8px rgba(0,0,0,0.05); }
    .saving-goals-table tbody td { padding: 1.25rem 1rem; vertical-align: middle; font-size: 0.875rem; border: none; }
    .saving-goals-table tbody td:first-child { border-top-left-radius: 0.75rem; border-bottom-left-radius: 0.75rem; }
    .saving-goals-table tbody td:last-child { border-top-right-radius: 0.75rem; border-bottom-right-radius: 0.75rem; text-align: right; }

    .progress-container .progress-info { display: flex; gap: 0.5rem; align-items: baseline; margin-bottom: 0.5rem; }
    .progress-container .percentage { font-weight: 700; font-size: 0.9rem; }
    .progress-container .status { color: #9CA3AF; font-size: 0.75rem; }
    .progress { height: 8px; border-radius: 8px; background-color: #F3F4F6; }
    .progress-bar { background-color: #16A34A; }

    .action-cell { display: flex; justify-content: flex-end; gap: 0.5rem; }
    .action-cell .btn { border-radius: 0.5rem; font-size: 0.8rem; font-weight: 500; padding: 0.4rem 0.8rem; }
    .btn-detail { background-color: #FEF9C3; border: 1px solid #FDE68A; color: #CA8A04; }
    .btn-detail:hover { background-color: #FDE68A; border-color: #FACC15; }
    .btn-tambah {
        background-color: var(--green-light);
        color: #1F2937;
    }
     .btn-tambah:hover {
        background-color: #c6e659; /* Sedikit lebih gelap dari var(--green-light) */
        color: #1F2937;
    }

</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    @php
        // Data user, ganti dengan data user yang sedang login
        $user = auth()->user();
    @endphp

    {{-- Header Halaman (Sama seperti halaman tabungan) --}}
    <header class="dashboard-header">
        <h1>Archived</h1>
        <div class="header-actions">
            <i class="bi bi-bell-fill"></i>
            <div class="user-profile dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none text-dark" data-bs-toggle="dropdown" aria-expanded="false">
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

    {{-- Action Bar: Search & Filter (Tanpa Tombol Tambah & Archived) --}}
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

    {{-- Tabel untuk menampilkan data dari database ($archivedGoals) --}}
    <table class="saving-goals-table">
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
                    <td class="fw-bold">{{ $goal->goal_name }}</td>
                    <td>{{ \Carbon\Carbon::parse($goal->deadline)->setTimezone('Asia/Jakarta')->format('d M Y') }}</td>
                    <td>Rp. {{ number_format($goal->target_amount, 0, ',', '.') }}</td>
                    <td class="fw-bold">Rp. {{ number_format($goal->saved_amount, 0, ',', '.') }}</td>
                    <td>
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
                    <td>
                        <div class="action-cell">
                            {{-- Tombol Detail untuk melihat rincian di modal --}}
                            <a href="#" class="btn btn-detail" data-bs-toggle="modal" data-bs-target="#detailGoalModal{{ $goal->id }}">Detail</a>
                            {{-- Di halaman arsip, biasanya tidak ada aksi lain --}}
                        </div>
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

@endsection