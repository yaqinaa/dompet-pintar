<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dompet Pintar - Landing Page</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
      font-family: 'Inter', sans-serif;
      margin: 0;
      padding: 0;
    }
    body {
      background: #f4f5f7;
      color: #1e1e1e;
    }
    header {
      background-color: white;
      padding: 1.5rem 4rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    header h1 {
      color: #3c3c3c;
      font-weight: 700;
    }
    nav a {
      margin-left: 2rem;
      text-decoration: none;
      color: #4f4f4f;
      font-weight: 500;
    }
    .hero {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 4rem;
      background-color: #ffffff;
    }
    .hero-text {
      max-width: 600px;
    }
    .hero-text h2 {
      font-size: 3rem;
      margin-bottom: 1rem;
    }
    .hero-text p {
      font-size: 1.1rem;
      margin-bottom: 2rem;
      color: #555;
    }
    .hero-text a {
      padding: 0.8rem 1.5rem;
      background-color: #b0f127;
      color: #1e1e1e;
      text-decoration: none;
      font-weight: 600;
      border-radius: 8px;
    }
    .hero-img img {
      width: 480px;
      border-radius: 1rem;
      box-shadow: 0 4px 16px rgba(0,0,0,0.1);
    }
    .features {
      padding: 4rem;
      background-color: #f9fafb;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 2rem;
    }
    .feature {
      background-color: white;
      padding: 2rem;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .feature h3 {
      margin-bottom: 1rem;
      font-size: 1.25rem;
    }
    .feature p {
      color: #555;
      font-size: 0.95rem;
    }
    footer {
      background-color: #1e1e1e;
      color: white;
      text-align: center;
      padding: 2rem;
    }
     .google-btn {
      display: inline-flex;
      align-items: center;
      background-color: #C8EE44;
      color: #1B212D;
      border: none;
      padding: 0.75rem 1.5rem;
      font-size: 1rem;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .google-btn:hover {
      background-color: #3367d6;
    }

    .google-btn img {
      width: 20px;
      height: 20px;
      margin-right: 0.8rem;
    }

    @media (max-width: 600px) {
      .hero-card {
        padding: 2rem;
      }

      .hero-card h1 {
        font-size: 2rem;
      }
    }
  </style>
</head>
<body>
  <header>
    <h1>Dompet Pintar</h1>
    <nav>
       <button class="google-btn" id="loginGoogleBtn">
        <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google Icon">
        Login dengan Google
      </button>
    </nav>
  </header>

  <section class="hero">
    <div class="hero-text">
      <h2>Kelola Keuanganmu Lebih Cerdas</h2>
      <p>Dompet Pintar bantu kamu mencatat pemasukan, pengeluaran, dan tabungan secara praktis. Pantau alokasi dana harian dengan visualisasi yang interaktif.</p>
      <a href="#download">Coba Sekarang</a>
    </div>
    <div class="hero-img">
      <img src="/assets/Untitled design.png" alt="Dashboard Dompet Pintar">

    </div>
  </section>

  <section class="features" id="fitur">
    <div class="feature">
      <h3>Statistik Interaktif</h3>
      <p>Grafik pemasukan dan pengeluaran harian yang mudah dipahami.</p>
    </div>
    <div class="feature">
      <h3>Alokasi Otomatis</h3>
      <p>Bagi pengeluaran ke dalam kategori Primer, Sekunder, Tersier, dan Tabungan.</p>
    </div>
    <div class="feature">
      <h3>Histori Transaksi</h3>
      <p>Lihat riwayat pemasukan, pengeluaran, dan tabungan dengan detail.</p>
    </div>
  </section>

  <footer>
    <p>&copy; 2025 Dompet Pintar. Semua hak dilindungi.</p>
  </footer>

    <!-- Script Google Login -->
  <script>
    document.getElementById('loginGoogleBtn').addEventListener('click', function (e) {
      e.preventDefault();
      const googleLoginUrl = "{{ url('auth/google') }}";
      const width = 600;
      const height = 600;
      const left = (screen.width - width) / 2;
      const top = (screen.height - height) / 2;

      const popup = window.open('/auth/google', 'Google Login', `width=${width},height=${height},top=${top},left=${left}`);

      const popupTick = setInterval(() => {
        if (popup.closed) {
          clearInterval(popupTick);
          window.location.reload();
        }
      }, 500);
    });

    window.addEventListener('message', function(event) {
      if (event.origin !== window.location.origin) return;
      if (event.data === 'google-login-success') {
        window.location.href = '/dashboard';
      }
    });
  </script>

  <!-- Link ke Bootstrap JS dan Popper.js -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>


</html>

