<?php
session_start();
require_once '../config.php';

if (isset($_SESSION['admin'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
$success = '';

if (isset($_SESSION['success_msg'])) {
    $success = $_SESSION['success_msg'];
    unset($_SESSION['success_msg']);
}

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']); 

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['admin'] = $username;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Login Admin | EduToraja</title>

    <!-- Google Fonts: Fraunces + Plus Jakarta Sans (senada website utama) -->
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;0,9..144,700;1,9..144,500&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* ===== PALET UKIRAN PA'SSURA ===== */
        :root {
            --ink: #191008;
            --ink-soft: #26180c;
            --red: #a5341f;
            --red-deep: #7e2415;
            --gold: #d9a13c;
            --gold-soft: #e8c37c;
            --cream: #f6efe2;
            --cream-deep: #ede1cc;
            --wood: #934b19;
            --text-main: #33241a;
            --text-light: #7d6c5c;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background:
                radial-gradient(ellipse at 20% 0%, rgba(147, 75, 25, 0.35), transparent 55%),
                radial-gradient(ellipse at 85% 100%, rgba(165, 52, 31, 0.28), transparent 55%),
                linear-gradient(160deg, var(--ink-soft), var(--ink));
            position: relative;
            overflow: hidden;
        }

        /* Pola belah ketupat ukiran samar di latar (SVG lokal, tanpa gambar eksternal) */
        body::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='56' height='56' viewBox='0 0 56 56'%3E%3Cpath d='M28 6 L50 28 L28 50 L6 28 Z' fill='none' stroke='%23d9a13c' stroke-width='1'/%3E%3Cpath d='M28 18 L38 28 L28 38 L18 28 Z' fill='none' stroke='%23a5341f' stroke-width='1'/%3E%3C/svg%3E");
            background-size: 56px 56px;
            opacity: 0.05;
            pointer-events: none;
        }

        /* ===== KARTU LOGIN ===== */
        .login-card {
            width: 100%;
            max-width: 410px;
            background: var(--cream);
            border-radius: 10px;
            border: 1px solid rgba(217, 161, 60, 0.35);
            box-shadow: 0 30px 70px -20px rgba(0, 0, 0, 0.6);
            overflow: hidden;
            position: relative;
            z-index: 1;
            animation: cardIn 0.6s cubic-bezier(0.22, 1, 0.36, 1);
        }
        @keyframes cardIn {
            from { opacity: 0; transform: translateY(26px) scale(0.98); }
            to   { opacity: 1; transform: translateY(0) scale(1); }
        }

        /* Balok ukiran zigzag di puncak kartu */
        .login-card::before {
            content: '';
            display: block;
            height: 10px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='10' viewBox='0 0 20 10'%3E%3Crect width='20' height='10' fill='%23191008'/%3E%3Cpath d='M0 10 L5 2 L10 10 Z' fill='%23d9a13c'/%3E%3Cpath d='M10 10 L15 2 L20 10 Z' fill='%23a5341f'/%3E%3C/svg%3E");
            background-repeat: repeat-x;
            background-size: 20px 10px;
        }

        .card-inner { padding: 38px 36px 30px; }

        /* ===== HEADER ===== */
        .brand {
            text-align: center;
            margin-bottom: 30px;
        }
        .brand-icon {
            width: 58px;
            height: 58px;
            margin: 0 auto 18px;
            border: 1px solid var(--gold);
            border-radius: 4px;
            transform: rotate(45deg);
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--ink);
        }
        .brand-icon i {
            transform: rotate(-45deg);
            color: var(--gold);
            font-size: 1.3rem;
        }
        .brand h1 {
            font-family: 'Fraunces', serif;
            font-size: 1.7rem;
            font-weight: 600;
            color: var(--ink);
        }
        .brand h1 em { font-style: italic; color: var(--red); }
        .brand p {
            font-size: 0.85rem;
            color: var(--text-light);
            margin-top: 5px;
            letter-spacing: 0.04em;
        }

        /* ===== ALERT ===== */
        .alert {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 13px 16px;
            border-radius: 6px;
            font-size: 0.87rem;
            font-weight: 500;
            margin-bottom: 22px;
            animation: shake 0.4s ease;
        }
        .alert.error {
            background: rgba(165, 52, 31, 0.1);
            border: 1px solid rgba(165, 52, 31, 0.4);
            color: var(--red-deep);
        }
        .alert.success {
            background: rgba(74, 106, 106, 0.12);
            border: 1px solid rgba(74, 106, 106, 0.4);
            color: #2c4c4c;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        /* ===== FORM ===== */
        .field { margin-bottom: 20px; }

        .field label {
            display: block;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--wood);
            margin-bottom: 8px;
        }

        .input-wrap { position: relative; }

        .input-wrap > i.lead {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
            font-size: 0.9rem;
            transition: color 0.25s;
            pointer-events: none;
        }

        .input-wrap input {
            width: 100%;
            padding: 13px 44px 13px 42px;
            border: 1px solid rgba(147, 75, 25, 0.35);
            border-radius: 6px;
            background: #fffdf8;
            font-family: inherit;
            font-size: 0.95rem;
            color: var(--text-main);
            outline: none;
            transition: border-color 0.25s, box-shadow 0.25s;
        }
        .input-wrap input::placeholder { color: #b5a691; }

        .input-wrap input:focus {
            border-color: var(--red);
            box-shadow: 0 0 0 3px rgba(165, 52, 31, 0.12);
        }
        .input-wrap input:focus ~ i.lead,
        .input-wrap:focus-within > i.lead { color: var(--red); }

        /* Tombol lihat/sembunyikan password */
        .toggle-pass {
            position: absolute;
            right: 6px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            color: var(--text-light);
            cursor: pointer;
            transition: color 0.25s, background 0.25s;
        }
        .toggle-pass:hover { color: var(--red); background: rgba(165, 52, 31, 0.08); }

        /* ===== TOMBOL MASUK ===== */
        .btn-login {
            width: 100%;
            padding: 14px;
            margin-top: 6px;
            background: var(--red);
            color: var(--cream);
            border: none;
            border-radius: 6px;
            font-family: inherit;
            font-size: 0.95rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: background 0.3s, transform 0.15s, box-shadow 0.3s;
        }
        .btn-login:hover {
            background: var(--red-deep);
            box-shadow: 0 12px 24px -10px rgba(165, 52, 31, 0.7);
        }
        .btn-login:active { transform: scale(0.98); }
        .btn-login i { transition: transform 0.3s; }
        .btn-login:hover i { transform: translateX(4px); }

        /* ===== TAUTAN BAWAH ===== */
        .card-footer {
            margin-top: 28px;
            padding-top: 22px;
            border-top: 1px solid rgba(147, 75, 25, 0.18);
            text-align: center;
        }

        .footer-links {
            display: flex;
            justify-content: space-between;
            font-size: 0.82rem;
            font-weight: 600;
            margin-bottom: 18px;
        }
        .footer-links a {
            color: var(--red);
            text-decoration: none;
            transition: color 0.25s;
        }
        .footer-links a:hover { color: var(--red-deep); text-decoration: underline; }

        .back-site {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.86rem;
            font-weight: 500;
            color: var(--text-light);
            text-decoration: none;
            transition: color 0.25s, gap 0.25s;
        }
        .back-site:hover { color: var(--ink); gap: 12px; }

        /* Keterangan kecil di bawah kartu */
        .under-card {
            position: relative;
            z-index: 1;
            text-align: center;
            margin-top: 20px;
            font-size: 0.78rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: rgba(232, 195, 124, 0.55);
        }

        .page-wrap { width: 100%; max-width: 410px; }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; }
        }
    </style>
</head>
<body>

    <div class="page-wrap">
        <div class="login-card">
            <div class="card-inner">

                <div class="brand">
                    <div class="brand-icon"><i class="fa-solid fa-mountain-sun"></i></div>
                    <h1>Edu<em>Toraja</em></h1>
                    <p>Panel Pengelolaan Konten &amp; Materi</p>
                </div>

                <?php if($error): ?>
                    <div class="alert error">
                        <i class="fa-solid fa-circle-exclamation"></i>
                        <?= $error ?>
                    </div>
                <?php endif; ?>

                <?php if($success): ?>
                    <div class="alert success">
                        <i class="fa-solid fa-circle-check"></i>
                        <?= $success ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="field">
                        <label for="username">Username</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-user lead"></i>
                            <input type="text" id="username" name="username" autocomplete="off" required placeholder="Masukkan username">
                        </div>
                    </div>

                    <div class="field">
                        <label for="password">Password</label>
                        <div class="input-wrap">
                            <i class="fa-solid fa-lock lead"></i>
                            <input type="password" id="password" name="password" required placeholder="••••••••">
                            <button type="button" class="toggle-pass" onclick="togglePassword(this)" aria-label="Tampilkan password">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" name="login" class="btn-login">
                        Masuk ke Dashboard <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </form>

                <div class="card-footer">
                    <div class="footer-links">
                        <a href="forgot_password.php">Lupa Password?</a>
                        <a href="register.php">Daftar Admin Baru</a>
                    </div>
                    <a href="../index.php" class="back-site">
                        <i class="fa-solid fa-arrow-left"></i> Kembali ke Website
                    </a>
                </div>

            </div>
        </div>

        <p class="under-card">Sistem Edukasi Pariwisata &bull; Tana Toraja</p>
    </div>

    <script>
        // Lihat / sembunyikan password
        function togglePassword(btn) {
            const input = document.getElementById('password');
            const icon = btn.querySelector('i');
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            icon.classList.toggle('fa-eye', !show);
            icon.classList.toggle('fa-eye-slash', show);
            btn.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
        }
    </script>
</body>
</html>