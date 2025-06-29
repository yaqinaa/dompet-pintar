<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dompet Pintar')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
     @stack('styles')
    <style>
        /* CSS Sidebar Bawaan Anda (TIDAK DIUBAH) */
        html, body { margin: 0; padding: 0; height: 100%; }
        .wrapper { display: flex; min-height: 100vh; }
        .sidebar { width: 240px; background-color: #f5f6f8; display: flex; flex-direction: column; justify-content: space-between; height: 100vh; position: sticky; top: 0; }
        .sidebar .top { padding:20px 20px; }
        .sidebar .brand { display: flex; align-items: center; margin-bottom: 32px; }
        .sidebar .brand img { height: 24px; margin-right: 8px; }
        .sidebar .brand span { font-size: 15px; font-weight: 600; color: #111827; }
        .sidebar a { display: flex; align-items: center; padding: 10px 20px; color: #9ca3af; font-size: 13.5px; font-weight: 500; text-decoration: none; transition: 0.2s; border-radius: 10px; }
        .sidebar a i { width: 18px; margin-right: 12px; text-align: center; }
        .sidebar a.active { background-color: #d7f864; color: #111827; font-weight: 600; }
        .sidebar a:hover:not(.active) { background-color: #e2e8f0; color: #1f2937; }
        .sidebar .bottom { padding: 0 20px 20px 20px; }
        .sidebar .bottom a { font-size: 13px; }

        .sidebar .dropdown-toggle {
            width: 100%;
            display: flex;
            /* PERBAIKAN: Gunakan space-between untuk mendorong panah ke kanan */
            justify-content: space-between; 
            align-items: center; /* Pastikan semua rata tengah vertikal */
        }

        .sidebar .dropdown-toggle::after {
            display: inline-block; /* Tampilkan kembali panah */
            border-top: .4em solid; /* Sedikit perbesar panah */
            border-right: .4em solid transparent;
            border-bottom: 0;
            border-left: .4em solid transparent;
            vertical-align: middle; /* Jaga agar tetap di tengah */
            transition: transform .2s ease-in-out; /* Animasi putar */
        }

        /* Animasi putar saat dropdown terbuka */
        .sidebar .dropdown-toggle[aria-expanded="true"]::after {
            transform: rotate(180deg);
        }

        /* ... (sisa style dropdown Anda tidak perlu diubah) ... */
        .sidebar .dropdown-menu {
            border: none;
            background-color: #f5f6f8;
            width: calc(100% - 20px);
            margin-left: 10px;
            box-shadow: none;
            padding: 5px;
        }
/* ... */
        .sidebar .dropdown-item {
            font-size: 13.5px;
            font-weight: 500;
            border-radius: 8px; /* Samakan radius dengan tombol lain */
            padding: 8px 15px;
        }
        .sidebar .dropdown-item.active, 
        .sidebar .dropdown-item:active {
            background-color: #d7f864;
            color: #111827;
            font-weight: 600;
        }

        /* ===================== CSS DASHBOARD FINAL (DENGAN IKON) ===================== */
        :root {
            --body-bg: #FFFFFF; --card-bg: #FFFFFF; --text-primary: #212529; --text-secondary: #8A92A6; 
            --border-color: #E9ECEF; --green-dark: #1E7A44; --green-light: #A3D959; --yellow-light: #E8E24A;
            --yellow-dark: #D4C936; --card-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); --card-border-radius: 12px;
        }
        .main-content {
            flex: 1; padding: 0px 32px; background-color: var(--body-bg);
            font-family: 'Inter', sans-serif; color: var(--text-primary);
        }
        .dashboard-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
        .dashboard-header h1 { font-weight: 700; font-size: 1rem; margin: 0; }
        .header-actions { display: flex; align-items: center; gap: 1.25rem; }
        .header-actions .bi { font-size: 1rem; cursor: pointer; color: var(--text-secondary); }
        .user-profile { display: flex; align-items: center; gap: 0.75rem; font-size: 0.75rem; }
        .user-profile img { width: 40px; height: 40px; border-radius: 50%; }
        .custom-card { background-color: var(--card-bg); border: 1px solid var(--border-color); border-radius: var(--card-border-radius); box-shadow: none; padding: 1.5rem; height: 100%; }

        /* --- KELAS BARU UNTUK SUMMARY CARD DENGAN IKON --- */
        .summary-card { padding: 1rem; } /* Padding diubah agar lebih pas dengan ikon */
        .summary-card-body {
            display: flex;
            align-items: center;
            gap: 1rem; /* Jarak antara ikon dan teks */
        }
        .summary-card-icon {
            width: 30px;
            height: 30px;
            border-radius: 10px; /* Sesuai gambar, rounded square bukan lingkaran penuh */
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0; /* Mencegah ikon mengecil */
        }
        .summary-card-icon i {
            font-size: 0.75rem; /* Ukuran ikon di dalam kotak */
        }

        /* Style untuk ikon di kartu GELAP (Total Saldo) */
        .summary-card.dark .summary-card-icon {
            background-color: rgba(163, 217, 89, 0.2); /* Hijau muda transparan */
        }
        .summary-card.dark .summary-card-icon i {
            color: var(--green-light); /* Warna ikon hijau muda */
        }

        /* Style untuk ikon di kartu PUTIH (Pengeluaran, Tabungan) */
        .custom-card:not(.dark) .summary-card-icon {
            background-color: #F0F2F5; /* Abu-abu sangat muda */
        }
        .custom-card:not(.dark) .summary-card-icon i {
            color: var(--text-secondary); /* Warna ikon abu-abu */
        }
        /* --- AKHIR KELAS BARU --- */

        .summary-card.dark { background-color: #2D3748; color: white; border: none; }
        .summary-card-title { font-size: 0.5rem; color: #A0AEC0; margin-bottom: 0.25rem; font-weight: 500; }
        .summary-card-amount { font-size: 0.8rem; font-weight: 700; }
        .card-header-flex { display: flex; justify-content: space-between; align-items: center; padding-bottom: 1rem; margin-bottom: 1rem; }
        .card-header-flex.with-border { border-bottom: 1px solid var(--border-color); }
        .card-header-flex h5 { margin: 0; font-weight: 600; font-size: 1rem; }
        .card-header-flex a { text-decoration: none; font-weight: 500; color: var(--green-dark); font-size: 0.75rem; }
        .transaction-table { width: 100%; border-collapse: collapse; }
        .transaction-table th, .transaction-table td { padding: 1rem 0.25rem; text-align: left; border-bottom: 1px solid var(--border-color); vertical-align: middle; font-size: 0.875rem; }
        .transaction-table th { font-size: 0.7rem; color: var(--text-secondary); text-transform: uppercase; font-weight: 500; padding-bottom: 0.5rem; }
        .transaction-table tr:last-child td { border-bottom: none; }
        .saving-transaction-item .fw-bold { font-size: 0.7rem; }
        .saving-transaction-item .text-muted { font-size: 0.8rem; }
        .tag { display: inline-block; padding: 0.2rem 0.65rem; border-radius: 12px; font-size: 0.75rem; font-weight: 500; }
        .tag-saving { background-color: #d1fae5; color: #065f46; }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="top">
                <div class="brand"><img src="{{ asset('assets/logo.png') }}" alt="Logo"><span>Dompet Pintar</span></div>
                <a href="{{ url('/dashboard') }}" class="{{ request()->is('dashboard') ? 'active' : '' }}"><i class="fas fa-home"></i> Beranda</a>
               <a href="{{ route('transactions.index') }}" class="{{ request()->routeIs('transactions.*') ? 'active' : '' }}"><i class="fas fa-exchange-alt"></i> Transaksi</a>
                <div class="dropdown">
                    {{-- Tombol utama dropdown --}}
                    <a href="#" class="dropdown-toggle {{ request()->routeIs('saving-goals.*') ? 'active' : '' }}" 
                    data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-piggy-bank"></i>
                            <span>Tabungan</span>
                        </div>
                    </a>
                    
                    {{-- Menu yang akan muncul saat dropdown diklik --}}
                    <ul class="dropdown-menu dropdown-menu-dark" style="border: none; background-color: #e2e8f0; width: 100%;">
                        <li>
                            {{-- Menggunakan route() untuk halaman Aktif --}}
                            <a class="dropdown-item {{ request()->routeIs('saving-goals.index') ? 'active' : '' }}" 
                            href="{{ route('saving-goals.index') }}">
                                Aktif
                            </a>
                        </li>
                        <li>
                            {{-- Menggunakan route() untuk halaman Archived, seperti saran Anda --}}
                            <a class="dropdown-item {{ request()->routeIs('saving-goals.archived') ? 'active' : '' }}" 
                            href="{{ route('saving-goals.archived') }}">
                                Archived
                            </a>
                        </li>
                    </ul>
                </div>
                <a href="{{ url('/income-allocations') }}" class="{{ request()->is('income-allocations') ? 'active' : '' }}"><i class="fas fa-chart-pie"></i> Alokasi</a>
            </div>
            <div class="bottom">
                <a href="{{ url('/settings') }}" class="{{ request()->is('settings') ? 'active' : '' }}"><i class="fas fa-cog"></i> Settings</a>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt"></i> Logout</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            </div>
        </div>

        <!-- Konten Utama -->
        <div class="main-content">
            @yield('content')
        </div>
    </div>

    <!-- Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/open-modal-edit-saving goals.js') }}"></script>
    @stack('scripts')
    
</body>
</html>