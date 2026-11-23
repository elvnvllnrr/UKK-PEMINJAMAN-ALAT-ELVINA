<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Peminjam - Sistem Peminjaman Alat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            min-height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #fff1f6 0%, #f8d8e6 40%, #f2bfd7 100%);
        }

        .register-card {
            width: min(100%, 560px);
            margin: 20px auto;
            padding: 40px;
            border-radius: 20px;
            background: #fff;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.18);
        }

        .register-header {
            margin-bottom: 28px;
            text-align: center;
        }

        .register-header i {
            margin-bottom: 12px;
            font-size: 48px;
            background: linear-gradient(135deg, #f7bfd3 0%, #ee8bb4 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .register-header h1 {
            margin-bottom: 8px;
            font-size: 24px;
            font-weight: 700;
            color: #2d3748;
        }

        .register-header p {
            margin: 0;
            color: #718096;
        }

        .form-control {
            min-height: 48px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 11px 14px;
        }

        .form-control:focus {
            border-color: #f48bb1;
            box-shadow: 0 0 0 3px rgba(244, 139, 177, 0.12);
        }

        .input-icon {
            width: 46px;
            border: 0;
            border-radius: 10px 0 0 10px;
            background: linear-gradient(135deg, #f7a9c3 0%, #eb7aa7 100%);
            color: #fff;
        }

        .btn-register {
            width: 100%;
            border: 0;
            border-radius: 10px;
            padding: 14px;
            background: linear-gradient(135deg, #f7a9c3 0%, #eb7aa7 100%);
            color: #fff;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(235, 122, 167, 0.35);
        }

        .role-note {
            border-radius: 10px;
            background: #fff7fb;
            color: #8d526d;
            font-size: 13px;
        }

        @media (max-width: 576px) {
            .register-card {
                margin: 12px;
                padding: 28px 20px;
            }
        }
    </style>
</head>
<body>
    <main class="register-card">
        <div class="register-header">
            <i class="fas fa-user-plus" aria-hidden="true"></i>
            <h1>Daftar akun peminjam</h1>
            <p>Buat akun untuk melihat katalog dan mengajukan peminjaman.</p>
        </div>

        @if($errors->any())
            <div class="alert alert-danger" role="alert">
                <i class="fas fa-exclamation-circle me-2" aria-hidden="true"></i>
                Periksa kembali data yang Anda masukkan.
            </div>
        @endif

        <form method="POST" action="{{ route('register', absolute: false) }}" novalidate>
            @csrf

            <div class="mb-3">
                <label for="name" class="form-label fw-semibold">Nama lengkap</label>
                <input
                    id="name"
                    type="text"
                    name="name"
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name') }}"
                    maxlength="255"
                    autocomplete="name"
                    required
                    autofocus
                >
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="username" class="form-label fw-semibold">Username</label>
                <div class="input-group">
                    <span class="input-group-text input-icon"><i class="fas fa-user" aria-hidden="true"></i></span>
                    <input
                        id="username"
                        type="text"
                        name="username"
                        class="form-control @error('username') is-invalid @enderror"
                        value="{{ old('username') }}"
                        maxlength="100"
                        pattern="[A-Za-z0-9_-]+"
                        autocomplete="username"
                        placeholder="Contoh: budi123"
                        required
                    >
                </div>
                @error('username')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">Email</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email') }}"
                    maxlength="255"
                    autocomplete="email"
                    required
                >
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="password" class="form-label fw-semibold">Password</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        minlength="8"
                        autocomplete="new-password"
                        required
                    >
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label fw-semibold">Ulangi password</label>
                    <input
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        class="form-control"
                        minlength="8"
                        autocomplete="new-password"
                        required
                    >
                </div>
            </div>

            <div class="role-note px-3 py-2 mt-3">
                <i class="fas fa-info-circle me-1" aria-hidden="true"></i>
                Registrasi ini otomatis membuat akun dengan role <strong>Peminjam</strong>.
            </div>

            <button type="submit" class="btn btn-register mt-4">
                <i class="fas fa-user-plus me-2" aria-hidden="true"></i>Daftar sekarang
            </button>
        </form>

        <div class="text-center mt-4">
            <span class="text-muted">Sudah punya akun?</span>
            <a href="{{ route('login', absolute: false) }}" class="fw-semibold text-decoration-none">Masuk</a>
        </div>
    </main>
</body>
</html>
