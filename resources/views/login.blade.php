<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Sistem Peminjaman Alat</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

{{-- Gaya khusus untuk login pengguna. --}}
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #fff1f6 0%, #f8d8e6 40%, #f2bfd7 100%);
            overflow: hidden;
            position: relative;
        }

        /* Mengatur latar animasi pada halaman login. */
        .bg-shapes {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 0;
        }

        .bg-shapes .shape {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 20s infinite;
        }

        .bg-shapes .shape:nth-child(1) {
            width: 300px;
            height: 300px;
            top: -100px;
            left: -100px;
            animation-delay: 0s;
        }

        .bg-shapes .shape:nth-child(2) {
            width: 200px;
            height: 200px;
            bottom: -50px;
            right: -50px;
            animation-delay: 5s;
        }

        .bg-shapes .shape:nth-child(3) {
            width: 150px;
            height: 150px;
            top: 50%;
            left: 50%;
            animation-delay: 10s;
        }

        .bg-shapes .shape:nth-child(4) {
            width: 100px;
            height: 100px;
            top: 20%;
            right: 20%;
            animation-delay: 15s;
        }

        @keyframes float {
            0%, 100% {
                transform: translate(0, 0) rotate(0deg);
            }
            33% {
                transform: translate(30px, -30px) rotate(120deg);
            }
            66% {
                transform: translate(-20px, 20px) rotate(240deg);
            }
        }

        /* Membentuk wadah utama tata letak login. */
        .login-container {
            display: flex;
            max-width: 1000px;
            width: 90%;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3);
            z-index: 1;
            position: relative;
            backdrop-filter: blur(10px);
        }

        /* Menata bagian identitas dan branding. */
        .login-left {
            flex: 1;
            background: linear-gradient(135deg, #f7bfd3 0%, #ef8fb4 45%, #e774a3 100%);
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .login-left::before {
            content: '';
            position: absolute;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            top: -50%;
            left: -50%;
            animation: pulse 4s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 0.5;
            }
            50% {
                transform: scale(1.1);
                opacity: 0.8;
            }
        }

        .brand-icon {
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
            position: relative;
            z-index: 1;
            animation: bounce 2s ease-in-out infinite;
        }

        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        .brand-icon i {
            font-size: 60px;
            color: white;
        }

        .login-left h1 {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 15px;
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .login-left p {
            font-size: 16px;
            opacity: 0.9;
            text-align: center;
            line-height: 1.6;
            position: relative;
            z-index: 1;
        }

        .feature-list {
            margin-top: 40px;
            position: relative;
            z-index: 1;
        }

        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .feature-item i {
            width: 35px;
            height: 35px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 16px;
        }

        /* Menata panel formulir login. */
        .login-right {
            flex: 1;
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-header {
            margin-bottom: 40px;
        }

        .login-header h2 {
            font-size: 28px;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 10px;
        }

        .login-header p {
            color: #718096;
            font-size: 14px;
        }

        /* Mengatur gaya bidang dan aksi formulir login. */
        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-label {
            font-size: 14px;
            font-weight: 600;
            color: #4a5568;
            margin-bottom: 8px;
            display: block;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0aec0;
            font-size: 18px;
            transition: all 0.3s ease;
        }

        .form-control {
            width: 100%;
            padding: 15px 20px 15px 55px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #f7fafc;
        }

        .form-control:focus {
            outline: none;
            border-color: #f48bb1;
            background: white;
            box-shadow: 0 0 0 4px rgba(244, 139, 177, 0.12);
        }

        .form-control:focus + i,
        .input-wrapper:focus-within i {
            color: #f48bb1;
        }

        .form-control::placeholder {
            color: #a0aec0;
        }

        /* Mengatur kontrol tampil atau sembunyikan kata sandi. */
        .password-toggle {
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #a0aec0;
            transition: all 0.3s ease;
        }

        .password-toggle:hover {
            color: #f48bb1;
        }

        /* Menata pilihan ingat login dan tautan lupa kata sandi. */
        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            font-size: 13px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .remember-me input {
            margin-right: 8px;
            cursor: pointer;
        }

        .remember-me span {
            color: #4a5568;
        }

        .forgot-password {
            color: #ee8bb4;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .forgot-password:hover {
            color: #d76f9d;
        }

        /* Mengatur tampilan tombol masuk. */
        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #f7a9c3 0%, #eb7aa7 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: all 0.5s ease;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(235, 122, 167, 0.35);
        }

        .btn-login:hover::before {
            left: 100%;
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .btn-login i {
            margin-right: 8px;
        }

        /* Menampilkan contoh akun untuk pengujian login. */
        .demo-accounts {
            margin-top: 30px;
            padding: 20px;
            background: #fff5f9;
            border-radius: 12px;
            border-left: 4px solid #ee8bb4;
        }

        .demo-accounts h6 {
            font-size: 13px;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 12px;
        }

        .demo-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            font-size: 12px;
            border-bottom: 1px solid #e2e8f0;
        }

        .demo-item:last-child {
            border-bottom: none;
        }

        .demo-item .role {
            font-weight: 600;
            color: #4a5568;
        }

        .demo-item .credentials {
            color: #718096;
            font-family: 'Courier New', monospace;
        }

        /* Mengatur gaya pesan status dan kesalahan login. */
        .alert {
            border-radius: 12px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 25px;
            font-size: 14px;
            animation: slideDown 0.5s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
            color: white;
        }

        .alert-success {
            background: linear-gradient(135deg, #51cf66 0%, #40c057 100%);
            color: white;
        }

        .alert i {
            margin-right: 10px;
        }

        /* Menyesuaikan tata letak login pada layar kecil. */
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                max-width: 450px;
            }

            .login-left {
                padding: 40px 30px;
            }

            .login-right {
                padding: 40px 30px;
            }

            .brand-icon {
                width: 80px;
                height: 80px;
            }

            .brand-icon i {
                font-size: 40px;
            }

            .login-left h1 {
                font-size: 24px;
            }

            .feature-list {
                display: none;
            }
        }

        /* Mengatur animasi proses saat login dikirim. */
        .btn-login.loading {
            pointer-events: none;
            opacity: 0.8;
        }

        .btn-login.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
        }

        @keyframes spin {
            to {
                transform: translateY(-50%) rotate(360deg);
            }
        }
    </style>
</head>
<body>

    <div class="bg-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <div class="login-container animate__animated animate__fadeIn">

        <div class="login-left">
            <div class="brand-icon">
                <i class="fas fa-tools"></i>
            </div>
            <h1>Sistem Peminjaman Alat</h1>
            <p>Kelola peminjaman alat dengan mudah, cepat, dan terorganisir</p>
            
            <div class="feature-list">
                <div class="feature-item">
                    <i class="fas fa-check"></i>
                    <span>Manajemen alat terpusat</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-check"></i>
                    <span>Tracking peminjaman real-time</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-check"></i>
                    <span>Laporan otomatis</span>
                </div>
            </div>
        </div>

        <div class="login-right">
            <div class="login-header">
                <h2>Selamat Datang! 👋</h2>
                <p>Silakan login untuk melanjutkan ke dashboard Anda</p>
            </div>

{{-- Tampilkan pemberitahuan keberhasilan yang disimpan pada sesi. --}}
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

{{-- Tampilkan ringkasan kesalahan setelah validasi formulir gagal. --}}
            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $errors->first() }}
                </div>
            @endif

{{-- Form untuk mengirim kredensial login pengguna. --}}
            <form method="POST" action="{{ route('login') }}">
    @csrf  {{-- ← WAJIB ADA! --}}

</form>
                
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <div class="input-wrapper">
                        <input type="text" class="form-control" name="username" value="{{ old('username') }}" placeholder="Masukkan username Anda" required autofocus>
                        <i class="fas fa-user"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-wrapper">
                        <input type="password" class="form-control" name="password" id="password" placeholder="Masukkan password Anda" required>
                        <i class="fas fa-lock"></i>
                        <span class="password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </span>
                    </div>
                </div>

                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember">
                        <span>Ingat saya</span>
                    </label>
                    <a href="#" class="forgot-password">Lupa password?</a>
                </div>

                <button type="submit" class="btn-login" id="loginBtn">
                    <i class="fas fa-sign-in-alt"></i>
                    Login
                </button>
            </form>

            <div class="demo-accounts">
                <h6><i class="fas fa-info-circle me-2"></i>Demo Accounts</h6>
                <div class="demo-item">
                    <span class="role">Admin</span>
                    <span class="credentials">admin / password</span>
                </div>
                <div class="demo-item">
                    <span class="role">Petugas</span>
                    <span class="credentials">petugas / password</span>
                </div>
                <div class="demo-item">
                    <span class="role">Peminjam</span>
                    <span class="credentials">peminjam / password</span>
                </div>
            </div>
        </div>
    </div>

{{-- Skrip klien mengatur toggle kata sandi, animasi pengiriman, dan fokus kolom login. --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

{{-- Skrip klien mengatur toggle kata sandi, animasi pengiriman, dan fokus kolom login. --}}
    <script>
        // Alihkan visibilitas kata sandi pada kolom masuk.
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('toggleIcon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Tampilkan animasi proses saat formulir login dikirim.
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            const btn = document.getElementById('loginBtn');
            btn.classList.add('loading');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
        });

        // Berikan efek visual saat pengguna memfokuskan kolom formulir.
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.02)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });
    </script>
</body>
</html>