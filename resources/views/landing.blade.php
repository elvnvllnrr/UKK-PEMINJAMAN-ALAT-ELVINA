<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#fff7fb">
    <meta name="description" content="Sistem Peminjaman Alat, layanan praktis untuk melihat katalog dan mengajukan peminjaman alat.">
    <title>Sistem Peminjaman Alat</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

{{-- Gaya khusus untuk halaman sambutan. --}}
    <style>
        :root {
            color-scheme: light;
            --pink-50: #fff7fb;
            --pink-100: #ffedf5;
            --pink-200: #fbd8e8;
            --pink-400: #ef9cbd;
            --pink-500: #e77da7;
            --pink-700: #a94770;
            --ink: #4b2d3f;
            --muted: #846d7b;
        }

        * { box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            margin: 0;
            color: var(--ink);
            background: var(--pink-50);
            font-family: 'Poppins', sans-serif;
        }

        a { color: inherit; text-decoration: none; }

        .container {
            width: min(1120px, calc(100% - 48px));
            margin: 0 auto;
        }

        .site-header {
            position: relative;
            z-index: 2;
            border-bottom: 1px solid rgba(225, 172, 196, .25);
            background: rgba(255, 247, 251, .85);
            backdrop-filter: blur(14px);
        }

        .navigation {
            min-height: 78px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
        }

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: -.3px;
        }

        .brand-icon {
            width: 42px;
            height: 42px;
            display: grid;
            place-items: center;
            color: white;
            border-radius: 14px;
            background: linear-gradient(135deg, #f2b5ce, #df709d);
            box-shadow: 0 8px 18px rgba(205, 104, 147, .2);
        }

        .nav-login, .button-primary {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            border-radius: 999px;
            color: white;
            font-size: 14px;
            font-weight: 600;
            background: linear-gradient(135deg, #ef9cbd, #db6e9a);
            box-shadow: 0 10px 22px rgba(205, 104, 147, .2);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .nav-login { padding: 11px 21px; }
        .button-primary { padding: 15px 25px; }

        .nav-login:hover, .button-primary:hover {
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 14px 26px rgba(205, 104, 147, .28);
        }

        .hero {
            position: relative;
            overflow: hidden;
            padding: 86px 0 95px;
            background:
                radial-gradient(ellipse at 78% 35%, rgba(251, 216, 232, .82), transparent 36%),
                linear-gradient(145deg, #fff9fc 0%, #fff1f7 53%, #fce4ef 100%);
        }

        .hero::before, .hero::after {
            content: "";
            position: absolute;
            border: 1px solid rgba(231, 125, 167, .13);
            border-radius: 50%;
            pointer-events: none;
        }

        .hero::before { width: 460px; height: 460px; right: -120px; top: -210px; }
        .hero::after { width: 330px; height: 330px; right: -55px; top: -145px; }

        .hero-grid {
            position: relative;
            z-index: 1;
            display: grid;
            grid-template-columns: 1.05fr .95fr;
            align-items: center;
            gap: 56px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            padding: 8px 13px;
            border: 1px solid #f5d3e2;
            border-radius: 999px;
            color: var(--pink-700);
            background: rgba(255, 255, 255, .68);
            font-size: 12px;
            font-weight: 600;
        }

        .eyebrow i { font-size: 11px; }

        h1 {
            max-width: 620px;
            margin: 22px 0 17px;
            font-size: clamp(38px, 5vw, 61px);
            font-weight: 800;
            letter-spacing: -2.7px;
            line-height: 1.13;
        }

        h1 span {
            color: var(--pink-500);
            display: block;
        }

        .hero-copy {
            max-width: 520px;
            margin: 0;
            color: var(--muted);
            font-size: 16px;
            line-height: 1.9;
        }

        .hero-actions {
            display: flex;
            align-items: center;
            gap: 22px;
            margin-top: 30px;
        }

        .text-link {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            color: var(--pink-700);
            font-size: 14px;
            font-weight: 600;
        }

        .text-link:hover { color: var(--pink-500); }

        .visual {
            min-height: 390px;
            position: relative;
            display: grid;
            place-items: center;
        }

        .visual-glow {
            position: absolute;
            width: 320px;
            height: 320px;
            border-radius: 50%;
            background: linear-gradient(145deg, #fbdde9, #f6bfd5);
            box-shadow: inset 0 0 0 18px rgba(255, 255, 255, .22);
        }

        .tool-card {
            width: min(310px, 80%);
            position: relative;
            z-index: 1;
            padding: 27px;
            border: 1px solid rgba(255, 255, 255, .85);
            border-radius: 24px;
            background: rgba(255, 255, 255, .88);
            box-shadow: 0 24px 70px rgba(159, 81, 116, .16);
            transform: rotate(-3deg);
        }

        .tool-card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .tool-icon {
            width: 54px;
            height: 54px;
            display: grid;
            place-items: center;
            border-radius: 17px;
            color: #c64f82;
            background: #ffedf5;
            font-size: 23px;
        }

        .availability {
            padding: 7px 10px;
            border-radius: 999px;
            color: #48826e;
            background: #e8f6ef;
            font-size: 10px;
            font-weight: 600;
        }

        .tool-card h2 { margin: 23px 0 5px; font-size: 18px; }
        .tool-card p { margin: 0; color: var(--muted); font-size: 12px; }

        .tool-placeholder {
            height: 92px;
            margin-top: 19px;
            display: grid;
            place-items: center;
            border-radius: 15px;
            color: #d979a2;
            background: linear-gradient(135deg, #fff2f7, #fbe2ed);
            font-size: 32px;
        }

        .floating-note {
            position: absolute;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 13px 16px;
            border: 1px solid rgba(255, 255, 255, .9);
            border-radius: 16px;
            background: rgba(255, 255, 255, .92);
            box-shadow: 0 14px 34px rgba(159, 81, 116, .12);
            font-size: 11px;
            font-weight: 600;
        }

        .floating-note i {
            width: 32px;
            height: 32px;
            display: grid;
            place-items: center;
            border-radius: 11px;
            color: #c64f82;
            background: #ffedf5;
            font-size: 14px;
        }

        .note-one { top: 54px; right: 0; }
        .note-two { bottom: 43px; left: 0; }

        .features {
            padding: 82px 0 90px;
            background: white;
        }

        .section-heading { max-width: 580px; margin: 0 auto 38px; text-align: center; }
        .section-heading .eyebrow { background: var(--pink-50); }
        .section-heading h2 { margin: 15px 0 10px; font-size: clamp(27px, 3vw, 36px); letter-spacing: -1px; }
        .section-heading p { margin: 0; color: var(--muted); font-size: 14px; line-height: 1.8; }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .feature-card {
            padding: 27px;
            border: 1px solid #f5e5ed;
            border-radius: 20px;
            background: linear-gradient(145deg, #fff, #fff9fc);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 15px 36px rgba(159, 81, 116, .09);
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            display: grid;
            place-items: center;
            border-radius: 15px;
            color: #c64f82;
            background: #ffedf5;
            font-size: 18px;
        }

        .feature-card h3 { margin: 19px 0 8px; font-size: 16px; }
        .feature-card p { margin: 0; color: var(--muted); font-size: 13px; line-height: 1.8; }

        .steps-section { padding: 78px 0; background: linear-gradient(120deg, #fff7fb, #fce8f1); }
        .steps { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
        .step { display: flex; align-items: flex-start; gap: 14px; }
        .step-number {
            flex: 0 0 38px;
            width: 38px;
            height: 38px;
            display: grid;
            place-items: center;
            border-radius: 13px;
            color: white;
            background: linear-gradient(135deg, #ef9cbd, #db6e9a);
            font-size: 13px;
            font-weight: 700;
        }
        .step h3 { margin: 1px 0 6px; font-size: 14px; }
        .step p { margin: 0; color: var(--muted); font-size: 12px; line-height: 1.7; }

        .closing { padding: 68px 0; background: white; }
        .closing-card {
            padding: 42px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 26px;
            border-radius: 25px;
            background:
                radial-gradient(circle at 90% 20%, rgba(255,255,255,.45), transparent 30%),
                linear-gradient(120deg, #f9dce9, #f5c6da);
        }
        .closing-card h2 { margin: 0 0 8px; font-size: clamp(22px, 3vw, 30px); letter-spacing: -.7px; }
        .closing-card p { margin: 0; color: #755166; font-size: 13px; }
        .closing-card .button-primary { flex: 0 0 auto; background: #fff; color: var(--pink-700); box-shadow: 0 8px 20px rgba(159, 81, 116, .12); }

        .site-footer { padding: 22px 0; background: white; }
        .footer-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            border-top: 1px solid #f3e4eb;
            padding-top: 20px;
            color: var(--muted);
            font-size: 11px;
        }
        .footer-inner .brand { color: var(--ink); font-size: 12px; }
        .footer-inner .brand-icon { width: 30px; height: 30px; border-radius: 10px; font-size: 12px; }

        @media (max-width: 760px) {
            .hero { padding: 65px 0 66px; }
            .hero-grid { grid-template-columns: 1fr; gap: 28px; }
            .hero-copy { font-size: 14px; }
            .visual { min-height: 340px; }
            .visual-glow { width: 280px; height: 280px; }
            .note-one { right: 3%; }
            .note-two { left: 3%; }
            .feature-grid { grid-template-columns: 1fr; }
            .steps { grid-template-columns: 1fr; gap: 20px; }
            .closing-card { align-items: flex-start; flex-direction: column; padding: 30px 25px; }
        }

        @media (max-width: 460px) {
            .container { width: min(100% - 32px, 1120px); }
            .navigation { min-height: 68px; }
            .brand { font-size: 12px; }
            .brand-icon { width: 38px; height: 38px; }
            .nav-login { padding: 10px 15px; font-size: 12px; }
            h1 { font-size: 39px; letter-spacing: -1.8px; }
            .hero-actions { align-items: flex-start; flex-direction: column; gap: 17px; }
            .visual { min-height: 310px; }
            .tool-card { padding: 22px; }
            .floating-note { padding: 10px 12px; font-size: 10px; }
            .note-one { top: 22px; }
            .note-two { bottom: 20px; }
            .footer-inner { align-items: flex-start; flex-direction: column; }
        }

        @media (prefers-reduced-motion: reduce) {
            html { scroll-behavior: auto; }
            *, *::before, *::after { transition-duration: .01ms !important; }
        }
    </style>
</head>
<body>

{{-- Pembuka atau identitas halaman. --}}
    <header class="site-header">
        <div class="container navigation">
            <a class="brand" href="{{ url('/') }}" aria-label="Sistem Peminjaman Alat, beranda">
                <span class="brand-icon"><i class="fa-solid fa-screwdriver-wrench" aria-hidden="true"></i></span>
                <span>Sistem Peminjaman Alat</span>
            </a>
            <div style="display:flex; gap:10px; align-items:center;">
                <a class="text-link" href="{{ route('register', absolute: false) }}">Daftar</a>
                <a class="nav-login" href="{{ route('login', absolute: false) }}">
                    Masuk <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </header>

{{-- Konten utama pada halaman sambutan. --}}
    <main>
{{-- Bagian konten khusus untuk halaman sambutan. --}}
        <section class="hero">
            <div class="container hero-grid">
                <div>
                    <span class="eyebrow"><i class="fa-solid fa-sparkles" aria-hidden="true"></i> Praktis, tertata, mudah digunakan</span>
                    <h1>Semua kebutuhan alat, <span>dalam satu tempat.</span></h1>
                    <p class="hero-copy">Temukan alat yang Anda butuhkan, ajukan peminjaman, dan pantau statusnya dengan lebih mudah melalui satu sistem.</p>
                    <div class="hero-actions">
                        <a class="button-primary" href="{{ route('register', absolute: false) }}">
                            Daftar sebagai peminjam <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                        </a>
                        <a class="text-link" href="#cara-kerja">
                            Lihat cara kerja <i class="fa-solid fa-arrow-down" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>

                <div class="visual" aria-label="Ilustrasi katalog alat dan peminjaman">
                    <div class="visual-glow"></div>
                    <div class="floating-note note-one">
                        <i class="fa-solid fa-circle-check" aria-hidden="true"></i>
                        Pengajuan lebih praktis
                    </div>
                    <article class="tool-card">
                        <div class="tool-card-top">
                            <span class="tool-icon"><i class="fa-solid fa-toolbox" aria-hidden="true"></i></span>
                            <span class="availability"><i class="fa-solid fa-circle" aria-hidden="true"></i> Siap dipinjam</span>
                        </div>
                        <h2>Katalog alat</h2>
                        <p>Pilih alat sesuai kebutuhan Anda</p>
                        <div class="tool-placeholder"><i class="fa-solid fa-screwdriver-wrench" aria-hidden="true"></i></div>
                    </article>
                    <div class="floating-note note-two">
                        <i class="fa-solid fa-list-check" aria-hidden="true"></i>
                        Status mudah dipantau
                    </div>
                </div>
            </div>
        </section>

{{-- Bagian konten khusus untuk halaman sambutan. --}}
        <section class="features" aria-labelledby="features-title">
            <div class="container">
                <div class="section-heading">
                    <span class="eyebrow">Lebih mudah setiap hari</span>
                    <h2 id="features-title">Peminjaman alat jadi sederhana</h2>
                    <p>Kelola kebutuhan peminjaman dengan informasi yang jelas dan proses yang lebih tertata.</p>
                </div>
                <div class="feature-grid">
                    <article class="feature-card">
                        <span class="feature-icon"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i></span>
                        <h3>Katalog yang mudah dicari</h3>
                        <p>Lihat daftar alat dan informasi ketersediaannya sebelum mengajukan peminjaman.</p>
                    </article>
                    <article class="feature-card">
                        <span class="feature-icon"><i class="fa-regular fa-paper-plane" aria-hidden="true"></i></span>
                        <h3>Ajukan dengan praktis</h3>
                        <p>Kirim pengajuan peminjaman melalui sistem tanpa proses pencatatan yang merepotkan.</p>
                    </article>
                    <article class="feature-card">
                        <span class="feature-icon"><i class="fa-solid fa-clock-rotate-left" aria-hidden="true"></i></span>
                        <h3>Pantau status peminjaman</h3>
                        <p>Periksa status pengajuan dan informasi pengembalian kapan saja setelah masuk.</p>
                    </article>
                </div>
            </div>
        </section>

{{-- Bagian konten khusus untuk halaman sambutan. --}}
        <section class="steps-section" id="cara-kerja" aria-labelledby="steps-title">
            <div class="container">
                <div class="section-heading">
                    <span class="eyebrow">Mulai dalam beberapa langkah</span>
                    <h2 id="steps-title">Cara menggunakan sistem</h2>
                    <p>Ikuti langkah mudah berikut untuk mengajukan peminjaman alat.</p>
                </div>
                <div class="steps">
                    <article class="step">
                        <span class="step-number">01</span>
                        <div><h3>Masuk ke akun Anda</h3><p>Gunakan akun yang telah terdaftar untuk membuka layanan.</p></div>
                    </article>
                    <article class="step">
                        <span class="step-number">02</span>
                        <div><h3>Pilih alat di katalog</h3><p>Temukan alat dan tentukan kebutuhan peminjaman Anda.</p></div>
                    </article>
                    <article class="step">
                        <span class="step-number">03</span>
                        <div><h3>Ajukan dan pantau</h3><p>Kirim pengajuan, lalu pantau statusnya melalui dashboard.</p></div>
                    </article>
                </div>
            </div>
        </section>

{{-- Bagian konten khusus untuk halaman sambutan. --}}
        <section class="closing">
            <div class="container">
                <div class="closing-card">
                    <div>
                        <h2>Siap menemukan alat yang Anda perlukan?</h2>
                        <p>Daftar sebagai peminjam untuk melihat katalog dan mengajukan peminjaman.</p>
                    </div>
                    <a class="button-primary" href="{{ route('register', absolute: false) }}">
                        Daftar sekarang <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="container footer-inner">
            <a class="brand" href="{{ url('/') }}">
                <span class="brand-icon"><i class="fa-solid fa-screwdriver-wrench" aria-hidden="true"></i></span>
                <span>Sistem Peminjaman Alat</span>
            </a>
            <span>&copy; {{ date('Y') }} Sistem Peminjaman Alat</span>
        </div>
    </footer>
</body>
</html>
