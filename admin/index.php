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
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Login Admin | EduToraja</title>
    
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
                <span class="material-symbols-outlined text-3xl">admin_panel_settings</span>
            </div>
            <h2 class="font-headline text-2xl font-bold text-on-surface tracking-tight">Admin Login</h2>
            <p class="text-sm text-on-surface-variant mt-1">Sistem Pengelolaan EduToraja</p>
        </div>

        <?php if($error): ?>
            <div class="bg-error-container text-on-error-container p-4 rounded-lg mb-6 flex items-center gap-3 text-sm font-medium">
                <span class="material-symbols-outlined">error</span>
                <?= $error ?>
            </div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="bg-secondary-container text-on-secondary-container p-4 rounded-lg mb-6 flex items-center gap-3 text-sm font-medium">
                <span class="material-symbols-outlined">check_circle</span>
                <?= $success ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-5">
                <label class="block text-sm font-bold text-on-surface-variant mb-2">Username</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant opacity-60">person</span>
                    <input type="text" name="username" class="w-full pl-10 pr-4 py-3 rounded-xl border border-outline-variant/50 focus:border-primary focus:ring-primary text-sm bg-surface-container-low transition-colors" autocomplete="off" required placeholder="Masukkan username">
                </div>
            </div>
            <div class="mb-8">
                <label class="block text-sm font-bold text-on-surface-variant mb-2">Password</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant opacity-60">lock</span>
                    <input type="password" name="password" class="w-full pl-10 pr-4 py-3 rounded-xl border border-outline-variant/50 focus:border-primary focus:ring-primary text-sm bg-surface-container-low transition-colors" required placeholder="••••••••">
                </div>
            </div>
            
            <button type="submit" name="login" class="w-full py-3.5 bg-primary text-on-primary rounded-xl font-bold hover:opacity-90 active:scale-[0.98] transition-all flex items-center justify-center gap-2">
                <span>Masuk ke Dashboard</span>
                <span class="material-symbols-outlined text-sm">arrow_forward</span>
            </button>
        </form>

        <div class="mt-8 pt-6 border-t border-outline-variant/20 flex flex-col gap-4 text-center">
            <div class="flex justify-between text-xs font-bold px-2">
                <a href="forgot_password.php" class="text-primary hover:underline">Lupa Password?</a>
                <a href="register.php" class="text-primary hover:underline">Daftar Admin Baru</a>
            </div>
            <a href="../index.php" class="text-sm font-medium text-on-surface-variant hover:text-on-surface inline-flex items-center justify-center gap-2 mt-2 transition-colors">
                <span class="material-symbols-outlined text-sm">west</span> Kembali ke Website
            </a>
        </div>

    </div>

</body>
</html>
