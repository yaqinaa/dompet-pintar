<!DOCTYPE html>
<html>
<head>
    <title>Login Selesai</title>
</head>
<body>
    <p>Login berhasil. Jendela ini akan tertutup otomatis...</p>
    <script>
        
        window.opener.postMessage('google-login-success', window.location.origin);
        setTimeout(() => {
            window.close();
        }, 500); 
    </script>
</body>
</html>
