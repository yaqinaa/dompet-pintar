<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Test Success</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center" style="min-height: 100vh;">
    <div class="card text-center p-5 shadow-sm">
        <h1 class="card-title text-success">✅ Berhasil!</h1>
        <p class="card-text lead">Permintaan POST berhasil mencapai Controller!</p>
        <p class="card-text">Ini menandakan route dan form Anda berfungsi dengan baik.</p>
        <hr>
        <h2 class="h5">Data yang diterima:</h2>
        <pre class="bg-light p-3 border rounded text-start">{{ json_encode($data, JSON_PRETTY_PRINT) }}</pre>
        <a href="{{ url()->previous() }}" class="btn btn-primary mt-3">Kembali ke Halaman Sebelumnya</a>
    </div>
</body>
</html>