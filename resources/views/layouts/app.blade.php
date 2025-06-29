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

        @media (max-width: 768px) {
        .sidebar {
            position: fixed;
            left: -240px;
            top: 0;
            z-index: 999;
            height: 100%;
            transition: left 0.3s ease;
        }

        .sidebar.show {
            left: 0;
        }

        .main-content {
            flex: 1;
            padding: 1rem;
            width: 100%;
        }

        .mobile-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem;
            background-color: #f5f6f8;
            border-bottom: 1px solid #ddd;
        }

        .mobile-toggle h1 {
            font-size: 1rem;
            font-weight: 600;
            margin: 0;
        }

        .mobile-toggle button {
            background: none;
            border: none;
            font-size: 1.25rem;
        }

        /* Supaya konten tidak dibelakang sidebar saat terbuka */
        body.sidebar-open {
            overflow: hidden;
        }

        .wrapper {
            flex-direction: column;
        }
    }

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
            <div class="mobile-toggle d-md-none">
                <h1>Dompet Pintar</h1>
                <button onclick="toggleSidebar()"><i class="fas fa-bars"></i></button>
            </div>
            @yield('content')
        </div>
    </div>

    <!-- Script -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/open-modal-edit-saving goals.js') }}"></script>
    @stack('scripts')
    <script>
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const body = document.body;
            sidebar.classList.toggle('show');
            body.classList.toggle('sidebar-open');
        }
    </script>
    
</body>
</html>