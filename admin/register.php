<?php
session_start();
require_once '../config.php';

if (isset($_SESSION['admin'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
$success = '';

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Password dan Konfirmasi Password tidak cocok!";
    } else {
        // Cek username sudah ada atau belum
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt_check->bind_param("s", $username);
        $stmt_check->execute();
        $res = $stmt_check->get_result();

        if ($res->num_rows > 0) {
            $error = "Username sudah digunakan, silakan pilih yang lain.";
        } else {
            $hashed_password = md5($password);
            
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashed_password);
            
            if ($stmt->execute()) {
                $_SESSION['success_msg'] = "Registrasi berhasil! Silakan login.";
                header("Location: index.php");
                exit;
            } else {
                $error = "Terjadi kesalahan pada server.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Register Admin | EduToraja</title>
    
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=Noto+Serif:wght@400;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "surface-container-lowest": "#ffffff",
                        "on-tertiary-fixed": "#410006",
                        "on-tertiary-fixed-variant": "#832428",
                        "on-primary-fixed": "#321200",
                        "tertiary": "#8f2d30",
                        "tertiary-container": "#ae4546",
                        "secondary": "#446464",
                        "on-background": "#1b1c1a",
                        "on-surface-variant": "#54433c",
                        "on-primary": "#ffffff",
                        "surface-dim": "#dbdad6",
                        "primary-fixed": "#ffdbc9",
                        "outline": "#87736b",
                        "on-secondary-fixed-variant": "#2c4c4c",
                        "inverse-on-surface": "#f2f0ed",
                        "surface-container-low": "#f5f3ef",
                        "primary-fixed-dim": "#ffb68c",
                        "inverse-primary": "#ffb68c",
                        "surface-container": "#efeeea",
                        "surface-variant": "#e4e2de",
                        "outline-variant": "#dac1b8",
                        "surface-bright": "#fbf9f5",
                        "surface-container-highest": "#e4e2de",
                        "on-primary-fixed-variant": "#753401",
                        "on-tertiary-container": "#ffe0df",
                        "on-error": "#ffffff",
                        "secondary-fixed-dim": "#abcdcd",
                        "inverse-surface": "#30312e",
                        "on-secondary-container": "#4a6a6a",
                        "secondary-container": "#c6e9e9",
                        "primary-container": "#9e5421",
                        "surface-container-high": "#eae8e4",
                        "on-secondary": "#ffffff",
                        "background": "#fbf9f5",
                        "on-primary-container": "#ffe1d2",
                        "secondary-fixed": "#c6e9e9",
                        "surface-tint": "#934b19",
                        "error": "#ba1a1a",
                        "on-secondary-fixed": "#002020",
                        "on-surface": "#1b1c1a",
                        "primary": "#803d0a",
                        "surface": "#fbf9f5",
                        "on-tertiary": "#ffffff",
                        "tertiary-fixed": "#ffdad8",
                        "error-container": "#ffdad6",
                        "tertiary-fixed-dim": "#ffb3b0",
                        "on-error-container": "#93000a"
                    },
                    fontFamily: {
                        "headline": ["Noto Serif"],
                        "body": ["Inter"],
                        "label": ["Inter"]
                    },
                    borderRadius: {"DEFAULT": "0.125rem", "lg": "0.25rem", "xl": "0.5rem", "full": "0.75rem"},
                },
            },
        }
    </script>
    <style>
        .material-symbols-outlined { font-variation-settings: "FILL" 0, "wght" 400, "GRAD" 0, "opsz" 24; }
        .pa-ssura-pattern {
            background-image: url(https://lh3.googleusercontent.com/aida-public/AB6AXuBmfrcgcELznYg_N8Y3DQjChqEpXFdulqEWhDfOwUK79iDFt37faV1aFOBIf_mt7xEEphg_ESUzJ76C0lQ7W8C7VzVKTvZ7dq44k110RJ0I2i48Si-D8XOUIes1oBvPcBOLABTazkgABHZfHetWWBfshVPsX-XMJV9tSo5HQeifRgyUUCoI8WSsOivJzmItVqyiXA7ykXIMClmNZmabiIeQ-E4AiLpZOAUt0Q6NOmzyGfvelxgBcQEY9-FHf_ba5ZXyp3XQ94gE75x3);
            opacity: 0.04;
        }
    </style>
</head>
<body class="bg-surface font-body text-on-surface antialiased min-h-screen flex items-center justify-center relative p-4">
    <div class="absolute inset-0 pa-ssura-pattern pointer-events-none"></div>

    <div class="bg-surface-container-lowest rounded-2xl shadow-lg border border-outline-variant/30 w-full max-w-md p-8 relative z-10">
        
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-primary-container text-on-primary-container rounded-full flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-3xl">person_add</span>
            </div>
            <h2 class="font-headline text-2xl font-bold text-on-surface tracking-tight">Daftar Admin Baru</h2>
            <p class="text-sm text-on-surface-variant mt-1">Buat akun pengelola sistem baru</p>
        </div>

        <?php if($error): ?>
            <div class="bg-error-container text-on-error-container p-4 rounded-lg mb-6 flex items-center gap-3 text-sm font-medium">
                <span class="material-symbols-outlined">error</span>
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-5">
                <label class="block text-sm font-bold text-on-surface-variant mb-2">Username Baru</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant opacity-60">person</span>
                    <input type="text" name="username" class="w-full pl-10 pr-4 py-3 rounded-xl border border-outline-variant/50 focus:border-primary focus:ring-primary text-sm bg-surface-container-low transition-colors" autocomplete="off" required placeholder="Masukkan username">
                </div>
            </div>
            <div class="mb-5">
                <label class="block text-sm font-bold text-on-surface-variant mb-2">Password</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant opacity-60">lock</span>
                    <input type="password" name="password" class="w-full pl-10 pr-4 py-3 rounded-xl border border-outline-variant/50 focus:border-primary focus:ring-primary text-sm bg-surface-container-low transition-colors" required placeholder="Masukkan password">
                </div>
            </div>
            <div class="mb-8">
                <label class="block text-sm font-bold text-on-surface-variant mb-2">Konfirmasi Password</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant opacity-60">verified_user</span>
                    <input type="password" name="confirm_password" class="w-full pl-10 pr-4 py-3 rounded-xl border border-outline-variant/50 focus:border-primary focus:ring-primary text-sm bg-surface-container-low transition-colors" required placeholder="Ulangi password">
                </div>
            </div>
            
            <button type="submit" name="register" class="w-full py-3.5 bg-primary text-on-primary rounded-xl font-bold hover:opacity-90 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                <span>Daftar Sekarang</span>
                <span class="material-symbols-outlined text-sm">how_to_reg</span>
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-outline-variant/20 text-center">
            <span class="text-sm text-on-surface-variant">Sudah punya akun? </span>
            <a href="index.php" class="text-sm font-bold text-primary hover:underline">Login di sini</a>
        </div>

    </div>

</body>
</html>
