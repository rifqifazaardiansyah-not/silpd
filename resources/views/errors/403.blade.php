<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak - {{ config('silpd.nama_aplikasi') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-container {
            text-align: center;
            color: white;
        }
        .error-code {
            font-size: 120px;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .error-title {
            font-size: 32px;
            margin-bottom: 15px;
        }
        .error-message {
            font-size: 16px;
            margin-bottom: 30px;
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code">403</div>
        <div class="error-title">Akses Ditolak</div>
        <div class="error-message">
            Anda tidak memiliki akses ke halaman ini.
            <br>
            Role Anda: <strong>{{ ucfirst(session('role')) }}</strong>
        </div>
        <a href="{{ session('role') ? route(session('role') . '.dashboard') : route('login') }}" 
           class="btn btn-light btn-lg mt-4">
            Kembali ke Dashboard
        </a>
    </div>
</body>
</html>
