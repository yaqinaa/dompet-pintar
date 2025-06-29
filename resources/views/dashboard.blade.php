@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    @php
        // HAPUS SEMUA DATA DUMMY DARI SINI
        $user = auth()->user();
    @endphp

    <header class="dashboard-header">
        <h1>Beranda</h1>
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
                    <li><a class="dropdown-item" href="#">Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#">Logout</a></li>
                </ul>
            </div>
        </div>
    </header>

    <div class="row g-4">
        {{-- KOLOM KIRI --}}
        <div class="col-lg-8">
            <div class="vstack gap-4">
                {{-- ======================== SUMMARY CARDS DENGAN DATA ASLI ======================== --}}
                <div class="row g-4">
                    {{-- Total Saldo --}}
                    <div class="col-md-4">
                        <div class="custom-card summary-card dark">
                            <div class="summary-card-body">
                                <div class="summary-card-icon"><i class="bi bi-wallet2"></i></div>
                                <div>
                                    <p class="summary-card-title">Total Saldo</p>
                                    <h3 class="summary-card-amount">Rp. {{ number_format($totalSaldo, 0, ',', '.') }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Total Pengeluaran (Bulan Ini) --}}
                    <div class="col-md-4">
                        <div class="custom-card summary-card">
                            <div class="summary-card-body">
                                <div class="summary-card-icon"><i class="bi bi-box-arrow-up"></i></div>
                                <div>
                                    <p class="summary-card-title">Pengeluaran Bulan Ini</p>
                                    <h3 class="summary-card-amount">Rp. {{ number_format($totalPengeluaranBulanIni, 0, ',', '.') }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Total Tabungan --}}
                    <div class="col-md-4">
                        <div class="custom-card summary-card">
                            <div class="summary-card-body">
                                <div class="summary-card-icon"><i class="bi bi-piggy-bank"></i></div>
                                <div>
                                    <p class="summary-card-title">Total Tabungan</p>
                                    <h3 class="summary-card-amount">Rp. {{ number_format($totalTabungan, 0, ',', '.') }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- GRAFIK KEUANGAN HARIAN (tidak ada perubahan di HTML) --}}
                
                <div class="custom-card">
    
                {{-- 1. Header: Sekarang hanya berisi Judul dan Filter --}}
                <div class="card-header-flex with-border">
                    <h5>Grafik Keuangan Harian</h5>
                    
                    {{-- Form Filter --}}
                    <form action="{{ route('dashboard') }}" method="GET" id="chartRangeForm">
                        <select name="days" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                            <option value="7" {{ request('days', 7) == 7 ? 'selected' : '' }}>7 Hari Terakhir</option>
                            <option value="30" {{ request('days') == 30 ? 'selected' : '' }}>30 Hari Terakhir</option>
                        </select>
                    </form>
                </div>
                {{-- 3. Legenda (Badge): Pindahkan ke sini, di bawah grafik --}}
                <div class="d-flex justify-content-center flex-wrap gap-3 pt-3" style="border-top: 1px solid #E2E8F0;">
                    <span class="badge bg-success bg-opacity-25 text-success border border-success">Pemasukan</span>
                    <span class="badge bg-warning bg-opacity-25 text-warning-emphasis border border-warning">Pengeluaran</span>
                    <span class="badge bg-primary bg-opacity-25 text-primary border border-primary">Setor Tabungan</span>
                    <span class="badge bg-secondary bg-opacity-25 text-secondary border border-secondary">Tarik Tabungan</span>
                </div>

                {{-- 2. Canvas untuk Grafik --}}
                <div style="height: 250px; padding: 1rem 0;">
                    <canvas id="dailyFinancialChart"></canvas>
                </div>

                

            </div>

                {{-- ======================== TRANSAKSI TERAKHIR DENGAN DATA ASLI ======================== --}}
                <div class="custom-card">
                    <div class="card-header-flex">
                        <h5>Transaksi Terakhir</h5>
                        <a href="{{ route('transactions.index') }}">Lihat Semua ></a>
                    </div>
                    <table class="transaction-table">
                        <thead><tr><th>Nama Transaksi</th><th>Kategori</th><th>Jumlah</th><th>Tanggal</th></tr></thead>
                        <tbody>
                            @forelse($recentTransactions as $transaction)
                            <tr>
                                <td>{{ $transaction->description }}</td>
                                <td>
                                    <span class="badge" style="background-color: {{ $transaction->expenseCategory->color ?? '#6c757d' }}20; color: {{ $transaction->expenseCategory->color ?? '#6c757d' }};">
                                        {{ $transaction->expenseCategory->name ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="fw-bold @if($transaction->type == 'income') text-success @else text-danger @endif">
                                    {{ $transaction->type == 'income' ? '+' : '-' }} Rp. {{ number_format($transaction->amount, 0, ',', '.') }}
                                </td>
                                <td>{{ \Carbon\Carbon::parse($transaction->date)->format('d M Y') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-3">Belum ada transaksi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN --}}
        <div class="col-lg-4">
            <div class="vstack gap-4">
                {{-- TRANSACTION STATISTICS (dengan tempat untuk legenda) --}}
                <div class="custom-card">
                    <div class="card-header-flex">
                        <h5>Transaction Statistics</h5>
                    </div>
                    
                    {{-- Canvas untuk chart --}}
                    <div style="height: 250px; position: relative;" class="mb-3">
                        <canvas id="allocationChart"></canvas>
                    </div>

                    {{-- Container untuk legenda badge yang akan diisi oleh JavaScript --}}
                    <div id="allocationLegend" class="d-flex flex-wrap justify-content-center gap-2">
                        {{-- Legenda akan muncul di sini --}}
                    </div>
                </div>
                
                {{-- ======================== TRANSAKSI TABUNGAN DENGAN DATA ASLI ======================== --}}
                <div class="custom-card">
                    <div class="card-header-flex">
                        <h5>Transaksi Tabungan</h5>
                        <a href="{{ route('transactions.index') }}">View All ></a>
                    </div>
                    <div class="vstack gap-3">
                        @forelse($savingTransactions as $transaction)
                        <div class="d-flex justify-content-between align-items-center saving-transaction-item">
                            <div>
                                <p class="mb-0 fw-bold">{{ $transaction->savingGoal->goal_name ?? 'Transaksi Tabungan' }}</p>
                                <small class="text-muted">{{ \Carbon\Carbon::parse($transaction->date)->format('d M, Y') }}</small>
                            </div>
                            <div class="text-end">
                                <p class="mb-0 fw-bold @if($transaction->type == 'saving_deposit') text-success @else text-danger @endif">
                                    {{ $transaction->type == 'saving_deposit' ? '+' : '-' }} Rp. {{ number_format($transaction->amount, 0, ',', '.') }}
                                </p>
                                <span class="tag {{ $transaction->type == 'saving_deposit' ? 'tag-saving' : 'tag-withdrawal' }}">
                                    {{ $transaction->type == 'saving_deposit' ? 'Menabung' : 'Penarikan' }}
                                </span>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-3 text-muted">Belum ada transaksi tabungan.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- CDN untuk Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0"></script>

<script>
    // Pastikan Chart.js dan plugin datalabels sudah terdaftar
    Chart.register(ChartDataLabels);

    // ====================================================================
    // PERBAIKAN 1: Ambil data dari variabel PHP yang benar
    // ====================================================================
    // Variabel $chartData dan $allocationData sudah dikirim dari controller
    const dailyChartData = @json($chartData);
    const allocationData = @json($allocationData);


    // ====================================================================
    // GRAFIK KEUANGAN HARIAN (LINE CHART)
    // ====================================================================
    const ctxLine = document.getElementById('dailyFinancialChart');
    if (ctxLine) { // Selalu cek jika elemen ada sebelum membuat chart
        new Chart(ctxLine.getContext('2d'), {
            type: 'line',
            data: {
                // Gunakan data dari variabel yang sudah di-decode JSON
                labels: dailyChartData.labels,
                 datasets: [
                {
                    label: 'Pemasukan',
                    data: dailyChartData.pemasukan,
                    borderColor: '#1E7A44', // Hijau
                    backgroundColor: 'rgba(30, 122, 68, 0.1)',
                    tension: 0.4, fill: true,
                }, {
                    label: 'Pengeluaran',
                    data: dailyChartData.pengeluaran,
                    borderColor: '#D97706', // Oranye/Kuning Tua
                    backgroundColor: 'rgba(217, 119, 6, 0.1)',
                    tension: 0.4, fill: true,
                }, {
                    label: 'Setor Tabungan',
                    data: dailyChartData.setor_tabungan, // Data baru
                    borderColor: '#2563EB', // Biru
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    tension: 0.4, fill: true,
                }, {
                    label: 'Tarik Tabungan',
                    data: dailyChartData.tarik_tabungan, // Data baru
                    borderColor: '#64748B', // Abu-abu
                    backgroundColor: 'rgba(100, 116, 139, 0.1)',
                    tension: 0.4, fill: true,
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { callback: function(value) { return value >= 1000 ? (value / 1000) + 'K' : value; }}},
                    x: { grid: { display: false }}
                },
                plugins: {
                    legend: { display: false },
                    datalabels: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) { label += ': '; }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                }
            }
        });
    }


    // ====================================================================
    // ALLOCATION STATISTICS (PIE CHART)
    // ====================================================================
    const ctxPie = document.getElementById('allocationChart');
const allocationLegendContainer = document.getElementById('allocationLegend');

// Cek jika elemen dan data ada
if (ctxPie && allocationData && allocationData.length > 0) {
    
    // ===================================================
    // LANGKAH 1: Urutkan data dari terbesar ke terkecil
    // ===================================================
    // Buat salinan agar tidak mengubah data asli jika diperlukan di tempat lain
    const sortedAllocationData = [...allocationData].sort((a, b) => b.persentase - a.persentase);

    // Sekarang, proses data yang sudah diurutkan
    const allocationLabels = sortedAllocationData.map(item => item.kategori);
    const allocationPercentages = sortedAllocationData.map(item => item.persentase);
    
    // Definisikan warna. Warna pertama (#1E7A44) akan selalu untuk data terbesar.
    const pieChartColors = ['#1E7A44', '#D4C936', '#A3D959', '#E8E24A', '#F2A03D', '#3B82F6', '#9333EA'];

    new Chart(ctxPie.getContext('2d'), {
        type: 'pie',
        data: {
            labels: allocationLabels,
            datasets: [{
                label: 'Allocation',
                data: allocationPercentages,
                backgroundColor: pieChartColors,
                borderColor: '#FFFFFF',
                borderWidth: 2,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                // Sembunyikan legenda default
                legend: {
                    display: false 
                },
                
                // ===================================================
                // LANGKAH 2: Nonaktifkan semua label di dalam chart
                // ===================================================
                datalabels: {
                    display: false,
                },

                // Tooltip tetap aktif saat hover
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) { label += ': '; }
                            if (context.parsed !== null) { label += context.parsed.toFixed(2) + '%'; }
                            return label;
                        }
                    }
                }
            }
        }
    });

    // ===============================================================
    // LANGKAH 3: Buat legenda kustom dari data yang sudah diurutkan
    // ===============================================================
    allocationLegendContainer.innerHTML = '';
    sortedAllocationData.forEach((item, index) => {
        const color = pieChartColors[index % pieChartColors.length]; 
        
        // Buat badge HANYA dengan nama kategori
        const legendItem = `
            <span class="badge" style="background-color: ${color}20; color: ${color}; border: 1px solid ${color};">
                ${item.kategori}
            </span>
        `;
        
        allocationLegendContainer.innerHTML += legendItem;
    });

} else if (ctxPie) {
    // ... (kode untuk menampilkan 'Tidak ada data' tetap sama) ...
}
</script>
@endpush