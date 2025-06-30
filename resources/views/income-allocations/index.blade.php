@extends('layouts.app')

@section('title', 'Alokasi Penghasilan - Dompet Pintar')

{{-- Menyertakan modal yang mungkin Anda butuhkan --}}
@include('components.modal-allocation')
@include('components.modal-edit-allocation')
@if($allocation)
    @include('components.modal-goal-allocation', [
            'allocation' => $allocation, // Pastikan variabel yang diperlukan diteruskan ke modal
            'tabunganTotal' => $tabunganTotal,
            'goals' => $goals,
            'existingAllocations' => $existingAllocations,
        ])
 @include('components.modal-expense-allocation') 
@endif
@push('styles')

<style>
    /* ======================================================== */
    /* == CSS BARU UNTUK HALAMAN ALOKASI SESUAI GAMBAR CONTOH == */
    /* ======================================================== */

    :root {
        /* Warna-warna dari gambar contoh */
        --color-dark: #2A3342;
        --color-light-gray: #F0F2F5;
        --color-text-secondary: #8A92A6;
        --color-green-primary: #198754;
        --color-green-light: #A3D959;
        --color-yellow-primary: #E8E24A;
        --color-red: #DC3545;
        --color-blue: #0D6EFD;
        --card-border-radius: 12px;
        --card-padding: 1.5rem;
    }

    .page-container {
        font-family: 'Inter', sans-serif;
    }

    /* --- Header Halaman --- */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }
    .page-header-left h1 {
        font-size: 1rem;
        font-weight: 600;
        margin: 0;
    }
    .page-header-left p {
        font-size: 0.7rem;
        color: var(--color-text-secondary);
        margin: 0.25rem 0 0 0;
    }
    .page-header-right {
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .month-filter-form {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin:0;
    }
    .month-filter-form label {
        font-size: 0.8rem;
        font-weight: 500;
    }
    .month-input {
        padding: 0.5rem 0.75rem;
        border: 1px solid #E2E8F0;
        border-radius: 8px;
        font-size: 0.7rem;
        background-color: white;
    }
    .action-buttons .btn {
        border-radius: 8px;
        font-weight: 500;
        padding: 0.4rem 0.8rem;
        font-size:0.8rem
    }
    .action-buttons form {
        margin: 0; /* hilangkan margin bawaan form */
    }
    
    .btn-edit-alloc { background-color: #E2E8F0; color: #1F2937; border: 1px solid #CBD5E1;}
    .btn-edit-alloc:hover { 
        background-color: #CBD5E1; /* Sedikit lebih gelap dari #E2E8F0 */
        color: #1F2937; /* Tetap sama atau bisa lebih gelap lagi jika diinginkan */
        border-color: #A0AEC0; /* Border juga sedikit gelap */
    }
    .btn-delete-alloc { background-color: var(--color-red); color: white; }
    .btn-delete-alloc:hover {
        background-color: #C82333; /* Sedikit lebih gelap dari #DC3545 */
        color: white; /* Tetap putih agar mudah dibaca */
        border-color: #C82333; /* Sesuaikan border color */
    }

    /* --- Stats Cards --- */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px; /* Jarak antar card */
        margin-bottom: 30px
    }
        /* Responsif untuk layar lebih kecil */
    @media (max-width: 1200px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr); /* 2 kolom untuk tablet */
        }
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr; /* 1 kolom untuk mobile */
        }
        .page-header-right {
        flex-direction: column;
        align-items: flex-end; /* supaya rata kanan */
        width: 100%;
        }

        .page-header-right form,
        .page-header-right .action-buttons {
            width: 100%;
        }

        .page-header-right .action-buttons {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .actions-table thead {
        display: none;
    }

    .actions-table, .actions-table tbody, .actions-table tr, .actions-table td {
        display: block;
        width: 100%;
    }

    .actions-table tr {
        margin-bottom: 1rem;
        border: 1px solid #E2E8F0;
        border-radius: 10px;
        padding: 0.75rem;
        background-color: white;
    }

    .actions-table td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        font-size: 0.75rem;
        border-bottom: 1px solid #f0f0f0;
    }

    .actions-table td::before {
        content: attr(data-label);
        font-weight: bold;
        color: var(--color-text-secondary);
    }

    .actions-table td:last-child {
        border-bottom: none;
    }
    }
    .stat-card {
        background-color: white;
        padding: var(--card-padding);
        border-radius: var(--card-border-radius);
        border: 1px solid #E2E8F0;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .stat-card.dark {
        background-color: var(--color-dark);
        color: white;
        border: none;
    }
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: var(--card-border-radius);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }
    .stat-card.dark .stat-icon { background-color: rgba(255, 255, 255, 0.1); }
    .stat-card:not(.dark) .stat-icon { background-color: var(--color-light-gray); color: var(--color-text-secondary); }
    
    .stat-label { font-size: 0.65rem; color: #A0AEC0; }
    .stat-card.dark .stat-label { color: #A0AEC0; }
    .stat-card.dark .stat-icon {color: var(--color-green-light, #A3D959); /* Warna ikon hijau muda */}
    .stat-card:not(.dark) .stat-label { color: var(--color-text-secondary); }
    .stat-value { font-size: 0.8rem; font-weight: 700; }

    /* --- Main Grid (Chart & Detail) --- */
    .main-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }
    .card-box {
        background-color: white;
        padding: var(--card-padding);
        border-radius: var(--card-border-radius);
        border: 1px solid #E2E8F0;
    }
    .card-title {
        font-size: 1rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: var(--color-dark);
    }
    .chart-wrapper {
        position: relative;
        height: 250px;
    }
    .legend-container { display: flex; justify-content: center; gap: 1rem; margin-top: 1rem; flex-wrap: wrap; }
    .legend-item { display: flex; align-items: center; gap: 0.5rem; font-size: 0.5rem; }
    .legend-color { width: 12px; height: 12px; border-radius: 50%; }
    
    .detail-list { display: flex; flex-direction: column; gap: 0.4rem; font-size: 0.8rem; font-weight:900; }
    .detail-item { display: flex; align-items: center; justify-content: space-between;  background-color: var(--color-light-gray, #F0F2F5); padding: 0.75rem 1rem; border-radius: 10px;}
    .detail-info { display: flex; align-items: center; gap: 1rem; }
    .detail-icon { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background-color: var(--color-green-primary); color: var(--color-light-gray); font-size: 1.15rem;}
    .detail-name { font-weight: 500; }
    .detail-amount { font-size: 0.7rem; color: var(--color-text-secondary); }
    .detail-progress { text-align: right; width: 100px; }
    .detail-percentage { font-weight: 600; font-size: 0.8rem; }
    .detail-progress .progress { height: 6px; background-color: var(--color-light-gray); border-radius: 6px; margin-top: 0.25rem; }
    .detail-progress .progress-bar { background-color: var(--color-green-primary); }

    /* --- Tabel Aksi --- */
    .actions-table { width: 100%; border-collapse: collapse; }
    .actions-table th, .actions-table td { padding: 1rem; text-align: left; border-bottom: 1px solid #E2E8F0; font-size: 0.8rem;}
    .actions-table thead th { color: var(--color-text-secondary); font-size: 0.75rem; text-transform: uppercase; font-weight: 500; border-bottom-width: 2px; }
    .actions-table tr:last-child td { border-bottom: none; }
    .btn-alokasi {
        background-color: var(--color-green-light); /* #A3D959 */
        color: var(--color-dark); /* #2A3342 */
        font-weight: 500;
        padding: 0.3rem 1rem;
        border-radius: 16px;
        font-size: 0.8rem;
        text-decoration: none;
        transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
    }
    .btn-alokasi:hover {
        background-color: #8EB543; /* Sedikit lebih gelap dari #A3D959 (sama seperti tombol Tambah Data) */
        color: var(--color-dark); /* Tetap sama atau bisa disesuaikan */
        border-color: #8EB543; /* Sesuaikan border color */
    }
    /* Style untuk tombol Tambah Data (Biru) */
    .action-buttons .btn-primary {
        background-color: var(--color-green-light);
        color: white;
        border-color: var(--color-green-light); /* <--- TAMBAHKAN BARIS INI */
        border-radius: 8px; /* Pastikan border-radius juga diaplikasikan */
        font-weight: 500;
        padding: 0.4rem 0.8rem;
        font-size: 0.8rem;
        transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease; /* Tambahkan transisi untuk border-color */
    }
    .action-buttons .btn-primary:hover {
        background-color: #c6e659; /* Sedikit lebih gelap dari var(--green-light) */
        color: #1F2937;
        border-color: #8EB543; /* <--- TAMBAHKAN BARIS INI */
    }

    /* Style untuk pesan peringatan inline */
    .alert-warning-inline {
        background-color: #FFF3CD;
        color: #664d03;
        border: 1px solid #FFECB5;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.7rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin:0;
    }
    .alert-info {
        font-size: 0.75rem; /* Ukuran font lebih kecil */
        padding: 0.7rem 1rem; /* Sesuaikan padding jika perlu */
    }

    /* Responsive */
    @media (max-width: 992px) { .main-grid { grid-template-columns: 1fr; } }
</style>
@endpush
@section('content')
    @php
        $user = auth()->user();
    @endphp
<div class="container-fluid py-4 page-container">
    {{-- Header utama yang konsisten --}}
    <header class="dashboard-header d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
    <h1 class="mb-0">Alokasi</h1>
    <div class="d-flex align-items-center gap-3">
        <i class="bi bi-bell-fill"></i>
        <div class="user-profile dropdown">
            <a href="#" class="d-flex align-items-center text-decoration-none text-dark" data-bs-toggle="dropdown" aria-expanded="false">
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

{{-- Header Halaman Spesifik --}}
<div class="page-header flex-wrap d-flex justify-content-between gap-3 align-items-start align-items-md-center">
    <div class="page-header-left">
        <h1>Alokasi Penghasilan</h1>
        <p>Kelola dan pantau alokasi penghasilan bulanan Anda</p>
    </div>

    <div class="page-header-right d-flex flex-column flex-md-row align-items-start align-items-md-center gap-2">
        <!-- Form Bulan -->
        <form method="GET" action="{{ route('income-allocations.index') }}" class="month-filter-form d-flex align-items-center gap-2 w-100 w-md-auto">
            <label for="tanggal" class="mb-0">Pilih Bulan:</label>
            <input type="month" id="tanggal" name="tanggal" class="month-input" value="{{ $filterDate ?? \Carbon\Carbon::now()->format('Y-m') }}" onchange="this.form.submit()">
        </form>

        <!-- Tombol Aksi -->
        <div class="action-buttons d-flex flex-column flex-md-row gap-2 w-100 w-md-auto">
            @if ($allocation)
                <button type="button" class="btn btn-edit-alloc w-100 w-md-auto" data-bs-toggle="modal" data-bs-target="#editDataModal">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <form action="{{ route('income-allocations.destroy', $allocation->id) }}" method="POST" class="w-100 w-md-auto">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-delete-alloc w-100 w-md-auto" onclick="return confirm('Yakin ingin menghapus data ini?')">
                        <i class="bi bi-trash-fill"></i> Hapus
                    </button>
                </form>
            @elseif (!$allocation && $canAddData)
                <button type="button" class="btn btn-primary w-100 w-md-auto" data-bs-toggle="modal" data-bs-target="#tambahDataModal">
                    <i class="bi bi-plus-lg"></i> Tambah Data
                </button>
            @elseif (!$allocation && !$canAddData)
                <div class="alert alert-warning-inline w-100 w-md-auto">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <span>Tidak bisa menambah data</span>
                </div>
            @endif
        </div>
    </div>
</div>

    @if (isset($message))
        <div class="alert alert-info">{{ $message }}</div>
    @else
        {{-- Kartu Statistik --}}
        <div class="stats-grid">
            {{-- 1. Periode --}}
            <div class="stat-card dark">
                <div class="stat-icon"><i class="fas fa-calendar"></i></div>
                <div>
                    <div class="stat-label">Periode</div>
                    <div class="stat-value">{{ \Carbon\Carbon::parse($filterDate ?? \Carbon\Carbon::now()->format('Y-m'))->format('F Y') }}</div>
                </div>
            </div>

            {{-- 2. Total Alokasi (Pemasukan yang Dialokasikan) --}}
            <div class="stat-card ">
                <div class="stat-icon"><i class="fas fa-wallet"></i></div>
                <div>
                    <div class="stat-label">Dana Alokasi</div>
                    <div class="stat-value">Rp. {{ number_format($totalIncomeAllocated, 0, ',', '.') }}</div>
                </div>
            </div>

            {{-- 3. Total Dana Sudah Dialokasikan --}}
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div> {{-- Icon baru, bisa disesuaikan --}}
                <div>
                    <div class="stat-label">Sudah Dialokasikan</div>
                    <div class="stat-value">Rp. {{ number_format($totalAllocatedAmount, 0, ',', '.') }}</div>
                </div>
            </div>

            {{-- 4. Total Dana Belum Dialokasikan --}}
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div> {{-- Icon baru, bisa disesuaikan --}}
                <div>
                    <div class="stat-label">Belum Dialokasikan</div>
                    <div class="stat-value">Rp. {{ number_format($remainingAllocationAmount, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        {{-- Grid Utama: Chart & Detail --}}
        <div class="main-grid">
            <div class="card-box">
                <h5 class="card-title"><i class="fas fa-chart-pie"></i> Visualisasi Alokasi</h5>
                <div class="chart-wrapper"><canvas id="allocationChart"></canvas></div>
                <div id="allocationLegend" class="legend-container"></div>
            </div>
            <div class="card-box">
                <h5 class="card-title"><i class="fas fa-list"></i> Detail Alokasi</h5>
                <div class="detail-list">
                    @foreach ($dataKategori as $kategori => $data)
                    <div class="detail-item">
                        <div class="detail-info"><div class="detail-icon"><i class="fas fa-tag"></i></div><div><div class="detail-name">{{ $kategori }}</div><div class="detail-amount">Rp. {{ number_format($data['nominal'], 0, ',', '.') }}</div></div></div>
                        <div class="detail-progress"><div class="detail-percentage">{{ $data['persen'] }}%</div><div class="progress"><div class="progress-bar" style="width: {{ $data['persen'] }}%"></div></div></div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Tabel Aksi --}}
        <div class="card-box">
            <h5 class="card-title"><i class="fas fa-cogs"></i> Aksi Kategori</h5>
            <table class="actions-table">
                <thead><tr><th>Kategori</th><th>Nominal</th><th>Presentase</th><th>Action</th></tr></thead>
                <tbody>
    @foreach ($dataKategori as $kategori => $data)
    <tr>
        <td data-label="Kategori">{{ $kategori }}</td>
        <td data-label="Nominal">Rp. {{ number_format($data['nominal'], 0, ',', '.') }}</td>
        <td data-label="Presentase">{{ $data['persen'] }}%</td>
        <td data-label="Action">
            @if ($kategori === 'Tabungan')
                <button type="button" class="btn btn-alokasi" data-bs-toggle="modal" data-bs-target="#allocationModal">
                    Alokasikan 
                </button>
            @else
                <button type="button"
                        class="btn btn-alokasi open-expense-allocation-modal"
                        data-bs-toggle="modal"
                        data-bs-target="#expenseAllocationModal"
                        data-category-id="{{ $dataKategori[$kategori]['allocation_category_id'] ?? '' }}">
                    Alokasikan 
                </button>
            @endif
        </td>
    </tr>
    @endforeach
</tbody>
            </table>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if (!isset($message) && !empty($dataKategori))
    const labels = @json(array_keys($dataKategori));
    const data = @json(array_values(array_column($dataKategori, 'persen')));
    const colors = ['#198754', '#E8E24A', '#A3D959', '#2A3342' ]; // Warna dari contoh

    const chartData = {
        labels: labels,
        datasets: [{ data: data, backgroundColor: colors, borderWidth: 0 }]
    };

    const config = {
        type: 'doughnut',
        data: chartData,
        options: {
            responsive: true, maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: { display: false }, // Kita buat legend sendiri
                tooltip: {
                    callbacks: { label: (context) => `${context.label}: ${context.parsed}%` }
                }
            }
        }
    };
    
    const ctx = document.getElementById('allocationChart').getContext('2d');
    const myChart = new Chart(ctx, config);

    // Membuat Legend Kustom
    const legendContainer = document.getElementById('allocationLegend');
    myChart.data.labels.forEach((label, index) => {
        const color = myChart.data.datasets[0].backgroundColor[index];
        const legendItem = document.createElement('div');
        legendItem.className = 'legend-item';
        legendItem.innerHTML = `<div class="legend-color" style="background-color: ${color}"></div><span>${label}</span>`;
        legendContainer.appendChild(legendItem);
    });
    @endif
});
</script>
@endpush