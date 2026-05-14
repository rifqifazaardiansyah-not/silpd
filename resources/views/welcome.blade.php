<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('silpd.nama_aplikasi') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .welcome-container {
            text-align: center;
            color: white;
        }
        
        .welcome-logo {
            font-size: 80px;
            margin-bottom: 30px;
        }
        
        .welcome-title {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .welcome-subtitle {
            font-size: 20px;
            opacity: 0.9;
            margin-bottom: 40px;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .welcome-description {
            font-size: 16px;
            opacity: 0.8;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
            margin-bottom: 50px;
            line-height: 1.6;
        }
        
        .btn-login {
            background-color: #e74c3c;
            border: none;
            padding: 15px 40px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s ease;
        }
        
        .btn-login:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        }
        
        .features {
            margin-top: 80px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            padding: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            backdrop-filter: blur(10px);
        }
        
        .feature-item {
            text-align: center;
        }
        
        .feature-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }
        
        .feature-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .feature-desc {
            font-size: 14px;
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <div class="welcome-logo">
            <i class="fas fa-warehouse"></i>
        </div>
        
        <div class="welcome-title">
            {{ config('silpd.nama_aplikasi') }}
        </div>
        
        <div class="welcome-subtitle">
            {{ config('silpd.nama_lengkap') }}
        </div>
        
        <div class="welcome-description">
            Sistem manajemen pencatatan dan pengelolaan lumbung pangan desa untuk membantu ketahanan pangan masyarakat.
        </div>
        
        @if(session('role'))
            <div>
                <p class="mb-3">Anda sudah login sebagai <strong>{{ ucfirst(session('role')) }}</strong></p>
                <a href="{{ route(session('role') . '.dashboard') }}" class="btn btn-login text-white">
                    Ke Dashboard
                </a>
            </div>
        @else
            <a href="{{ route('login') }}" class="btn btn-login text-white">
                Login Sekarang
            </a>
        @endif
        
        <div class="features">
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-leaf"></i>
                </div>
                <div class="feature-title">Pencatatan Panen</div>
                <div class="feature-desc">Catat hasil panen dengan mudah dan terstruktur</div>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-th"></i>
                </div>
                <div class="feature-title">Manajemen Slot</div>
                <div class="feature-desc">Kelola penyimpanan gabah per slot yang terorganisir</div>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="feature-title">Monitoring Stok</div>
                <div class="feature-desc">Monitor stok gabah desa secara real-time</div>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-arrow-alt-down"></i>
                </div>
                <div class="feature-title">FIFO System</div>
                <div class="feature-desc">Sistem First In First Out otomatis</div>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-bell"></i>
                </div>
                <div class="feature-title">Notifikasi</div>
                <div class="feature-desc">Notifikasi otomatis untuk kondisi kritis</div>
            </div>
            
            <div class="feature-item">
                <div class="feature-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="feature-title">Multi-Role</div>
                <div class="feature-desc">Admin, Pengelola, dan Petani dengan akses terbatas</div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
