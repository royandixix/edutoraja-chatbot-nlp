<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}

// Cek dan tambahkan kolom foto_url jika belum ada
$check_column = $conn->query("SHOW COLUMNS FROM users LIKE 'foto_url'");
if ($check_column && $check_column->num_rows == 0) {
    $conn->query("ALTER TABLE users ADD COLUMN foto_url VARCHAR(255) DEFAULT NULL");
}

// Mengambil jumlah statistik
$count_destinasi = $conn->query("SELECT COUNT(*) as total FROM destinasi")->fetch_assoc()['total'];
$count_budaya = $conn->query("SELECT COUNT(*) as total FROM budaya")->fetch_assoc()['total'];
$count_materi = $conn->query("SELECT COUNT(*) as total FROM materi")->fetch_assoc()['total'];
$count_kb = $conn->query("SELECT COUNT(*) as total FROM knowledge_base")->fetch_assoc()['total'];
$count_chat = $conn->query("SELECT COUNT(*) as total FROM chat_logs")->fetch_assoc()['total'];

// Metrik Chat AI
$avg_similarity = 0;
$count_high = 0;
$count_med = 0;
$count_low = 0;

if ($count_chat > 0) {
    $avg_similarity = $conn->query("SELECT AVG(similarity_score) as avg_score FROM chat_logs")->fetch_assoc()['avg_score'];
    $avg_similarity = round($avg_similarity * 100, 1);
    
    $count_high = $conn->query("SELECT COUNT(*) as total FROM chat_logs WHERE similarity_score >= 0.5")->fetch_assoc()['total'];
    $count_med = $conn->query("SELECT COUNT(*) as total FROM chat_logs WHERE similarity_score >= 0.2 AND similarity_score < 0.5")->fetch_assoc()['total'];
    $count_low = $conn->query("SELECT COUNT(*) as total FROM chat_logs WHERE similarity_score < 0.2")->fetch_assoc()['total'];
}

// 5 Chat terbaru
$recent_chats = $conn->query("SELECT * FROM chat_logs ORDER BY timestamp DESC LIMIT 5");

require_once 'profile_helper.php';
?>
<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Dashboard Admin | EduToraja</title>
    
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
        /* Modal Styles */
        .modal-backdrop { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; backdrop-filter: blur(2px); }
        .modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; z-index: 101; width: 90%; max-width: 500px; border-radius: 12px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); padding: 24px; }
        .modal.active, .modal-backdrop.active { display: block; }
    </style>
</head>
<body class="bg-surface font-body text-on-surface antialiased">

<!-- SideNavBar -->
<aside class="h-screen w-64 fixed left-0 top-0 bg-stone-100 flex flex-col py-6 z-50">
    <div class="px-8 mb-10">
        <h1 class="text-lg font-bold font-headline text-orange-900 tracking-tight">Curator Admin</h1>
        <p class="text-xs text-on-surface-variant font-medium opacity-70">EduToraja System</p>
    </div>
    <nav class="flex-1 space-y-1">
        <a class="bg-stone-50 text-orange-900 font-bold rounded-l-full ml-4 pl-4 py-3 flex items-center gap-3 transition-all duration-200" href="dashboard.php">
            <span class="material-symbols-outlined">dashboard</span>
            <span class="text-sm">Dashboard</span>
        </a>
        <a class="text-stone-500 pl-8 py-3 flex items-center gap-3 hover:bg-stone-200 rounded-lg transition-all duration-200" href="knowledge_base.php">
            <span class="material-symbols-outlined">dataset</span>
            <span class="text-sm font-medium">Knowledge Base</span>
        </a>
        <a class="text-stone-500 pl-8 py-3 flex items-center gap-3 hover:bg-stone-200 rounded-lg transition-all duration-200" href="destinasi.php">
            <span class="material-symbols-outlined">landscape</span>
            <span class="text-sm font-medium">Kelola Destinasi</span>
        </a>
        <a class="text-stone-500 pl-8 py-3 flex items-center gap-3 hover:bg-stone-200 rounded-lg transition-all duration-200" href="budaya.php">
            <span class="material-symbols-outlined">festival</span>
            <span class="text-sm font-medium">Kelola Budaya</span>
        </a>
        <a class="text-stone-500 pl-8 py-3 flex items-center gap-3 hover:bg-stone-200 rounded-lg transition-all duration-200" href="materi.php">
            <span class="material-symbols-outlined">menu_book</span>
            <span class="text-sm font-medium">Kelola Materi</span>
        </a>
        <a class="text-stone-500 pl-8 py-3 flex items-center gap-3 hover:bg-stone-200 rounded-lg transition-all duration-200" href="chat_logs.php">
            <span class="material-symbols-outlined">forum</span>
            <span class="text-sm font-medium">Chat Logs</span>
        </a>
        <a class="text-stone-500 pl-8 py-3 flex items-center gap-3 hover:bg-stone-200 rounded-lg transition-all duration-200" href="../index.php" target="_blank">
            <span class="material-symbols-outlined">public</span>
            <span class="text-sm">Lihat Website</span>
        </a>
    </nav>
    <div class="px-6 mt-auto">
        <div class="pt-6 border-t border-outline-variant/20">
            <a class="text-stone-500 pl-4 py-3 flex items-center gap-3 hover:text-error transition-all duration-200" href="logout.php">
                <span class="material-symbols-outlined">logout</span>
                <span class="text-sm font-medium">Logout</span>
            </a>
        </div>
    </div>
</aside>

<!-- Main Content -->
<main class="ml-64 min-h-screen p-10 relative">
    <div class="absolute inset-0 pa-ssura-pattern pointer-events-none"></div>

    <header class="flex justify-between items-end mb-10 relative z-10">
        <div>
            <h2 class="font-headline text-4xl text-on-surface font-bold">Ringkasan Sistem</h2>
            <p class="font-body text-on-surface-variant mt-2">Selamat datang kembali, <strong><?= htmlspecialchars($_SESSION['admin']) ?></strong>. Berikut adalah performa dan statistik edukasi pariwisata Toraja saat ini.</p>
        </div>
        <div onclick="openProfileModal()" class="flex items-center gap-4 bg-surface-container-low p-2 rounded-full px-6 cursor-pointer hover:bg-surface-container-high transition-all">
            <div class="text-right">
                <p class="text-xs font-bold text-primary"><?= htmlspecialchars($_SESSION['admin']) ?></p>
                <p class="text-[10px] text-on-surface-variant">Administrator</p>
            </div>
            <div class="w-10 h-10 rounded-full overflow-hidden bg-primary flex items-center justify-center text-white font-bold">
                <?php if (!empty($current_admin_foto) && file_exists("../" . $current_admin_foto)): ?>
                    <img src="../<?= htmlspecialchars($current_admin_foto) ?>" alt="Avatar" class="w-full h-full object-cover">
                <?php else: ?>
                    <?= strtoupper(substr($_SESSION['admin'], 0, 1)) ?>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <?php if(isset($_SESSION['msg_error'])): ?>
        <div id="alert-error-msg" class="bg-error-container text-on-error-container p-4 rounded-lg mb-8 relative z-10 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined">error</span>
                <p class="text-sm font-medium"><?= $_SESSION['msg_error'] ?></p>
            </div>
            <button onclick="document.getElementById('alert-error-msg').style.display='none'" class="text-on-error-container hover:bg-black/10 p-1 rounded-full transition-colors flex items-center justify-center">
                <span class="material-symbols-outlined text-sm">close</span>
            </button>
        </div>
        <?php unset($_SESSION['msg_error']); ?>
    <?php endif; ?>

    <?php if(isset($_SESSION['msg'])): ?>
        <div id="alert-success-msg" class="bg-secondary-container text-on-secondary-container p-4 rounded-lg mb-8 relative z-10 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined">check_circle</span>
                <p class="text-sm font-medium"><?= $_SESSION['msg'] ?></p>
            </div>
            <button onclick="document.getElementById('alert-success-msg').style.display='none'" class="text-on-secondary-container hover:bg-black/10 p-1 rounded-full transition-colors flex items-center justify-center">
                <span class="material-symbols-outlined text-sm">close</span>
            </button>
        </div>
        <?php unset($_SESSION['msg']); ?>
    <?php endif; ?>

    <!-- Welcome Gradient Card -->
    <section class="relative z-10 mb-8 overflow-hidden rounded-2xl bg-gradient-to-r from-orange-800 to-amber-700 p-8 text-white shadow-md">
        <div class="relative z-10 max-w-xl">
            <span class="bg-amber-500/20 text-amber-200 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">EduToraja Control Panel</span>
            <h3 class="font-headline text-2xl md:text-3xl font-bold mt-4 mb-2 text-primary-fixed">Halo, Administrator!</h3>
            <p class="text-white/80 text-sm leading-relaxed mb-6">Dashboard admin dirancang untuk membantu Anda memantau dan mengelola konten pariwisata, materi budaya, dan performa kecerdasan buatan (NLP Chatbot) dalam memberikan informasi Toraja yang akurat kepada publik.</p>
            <div class="flex flex-wrap gap-4">
                <a href="knowledge_base.php" class="bg-white text-orange-950 text-xs font-bold px-5 py-3 rounded-full hover:bg-orange-50 active:scale-95 transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">settings_suggest</span>
                    <span>Kelola AI Chatbot</span>
                </a>
                <a href="../index.php" target="_blank" class="bg-transparent border border-white/30 hover:border-white text-white text-xs font-bold px-5 py-3 rounded-full active:scale-95 transition-all flex items-center gap-2">
                    <span class="material-symbols-outlined text-sm">open_in_new</span>
                    <span>Kunjungi Website Publik</span>
                </a>
            </div>
        </div>
        <!-- Background Decorative Icon -->
        <div class="absolute -right-10 -bottom-10 opacity-10 text-white select-none">
            <span class="material-symbols-outlined !text-[200px]">travel_explore</span>
        </div>
    </section>

    <!-- Quick Stats Grid -->
    <section class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8 relative z-10">
        <!-- Destinasi -->
        <div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/20 shadow-sm flex flex-col justify-between hover:scale-[1.02] transition-transform">
            <div class="flex justify-between items-start mb-3">
                <div class="p-2.5 bg-orange-100 text-orange-900 rounded-lg">
                    <span class="material-symbols-outlined text-xl">landscape</span>
                </div>
            </div>
            <div>
                <h4 class="text-on-surface-variant text-xs font-medium">Destinasi Wisata</h4>
                <p class="text-2xl font-headline font-bold text-on-surface mt-1"><?= $count_destinasi ?></p>
            </div>
        </div>
        <!-- Budaya -->
        <div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/20 shadow-sm flex flex-col justify-between hover:scale-[1.02] transition-transform">
            <div class="flex justify-between items-start mb-3">
                <div class="p-2.5 bg-amber-100 text-amber-900 rounded-lg">
                    <span class="material-symbols-outlined text-xl">festival</span>
                </div>
            </div>
            <div>
                <h4 class="text-on-surface-variant text-xs font-medium">Kebudayaan</h4>
                <p class="text-2xl font-headline font-bold text-on-surface mt-1"><?= $count_budaya ?></p>
            </div>
        </div>
        <!-- Materi -->
        <div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/20 shadow-sm flex flex-col justify-between hover:scale-[1.02] transition-transform">
            <div class="flex justify-between items-start mb-3">
                <div class="p-2.5 bg-stone-100 text-stone-900 rounded-lg">
                    <span class="material-symbols-outlined text-xl">menu_book</span>
                </div>
            </div>
            <div>
                <h4 class="text-on-surface-variant text-xs font-medium">Materi Edukasi</h4>
                <p class="text-2xl font-headline font-bold text-on-surface mt-1"><?= $count_materi ?></p>
            </div>
        </div>
        <!-- Knowledge Base -->
        <div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/20 shadow-sm flex flex-col justify-between hover:scale-[1.02] transition-transform">
            <div class="flex justify-between items-start mb-3">
                <div class="p-2.5 bg-rose-100 text-rose-900 rounded-lg">
                    <span class="material-symbols-outlined text-xl">dataset</span>
                </div>
            </div>
            <div>
                <h4 class="text-on-surface-variant text-xs font-medium">Knowledge Base</h4>
                <p class="text-2xl font-headline font-bold text-on-surface mt-1"><?= $count_kb ?></p>
            </div>
        </div>
        <!-- Total Interaksi -->
        <div class="bg-surface-container-lowest p-5 rounded-xl border border-outline-variant/20 shadow-sm flex flex-col justify-between hover:scale-[1.02] transition-transform">
            <div class="flex justify-between items-start mb-3">
                <div class="p-2.5 bg-teal-100 text-teal-900 rounded-lg">
                    <span class="material-symbols-outlined text-xl">forum</span>
                </div>
            </div>
            <div>
                <h4 class="text-on-surface-variant text-xs font-medium">Total Interaksi Chat</h4>
                <p class="text-2xl font-headline font-bold text-on-surface mt-1"><?= $count_chat ?></p>
            </div>
        </div>
    </section>

    <!-- Analytics & Interaction Logs -->
    <section class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-10 relative z-10">
        
        <!-- NLP AI Metrics Card -->
        <div class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/20 shadow-sm flex flex-col">
            <h3 class="font-headline text-lg font-bold text-on-surface flex items-center gap-2 mb-6">
                <span class="material-symbols-outlined text-primary">analytics</span>
                <span>Analisis Akurasi NLP Chatbot</span>
            </h3>
            
            <div class="text-center py-6 bg-surface-container-low rounded-xl mb-6">
                <p class="text-xs text-on-surface-variant font-medium">Rata-rata Skor Kemiripan (TF-IDF)</p>
                <h4 class="text-4xl font-headline font-bold text-primary mt-1"><?= $avg_similarity ?>%</h4>
                <p class="text-[10px] text-teal-700 font-bold mt-2 flex items-center justify-center gap-1">
                    <span class="material-symbols-outlined text-xs">check_circle</span> Status Mesin AI Optimal
                </p>
            </div>

            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-xs font-bold text-on-surface-variant mb-1">
                        <span>Akurasi Tinggi (>= 50%)</span>
                        <span class="text-primary"><?= $count_high ?> Chat</span>
                    </div>
                    <div class="w-full bg-stone-200 h-2.5 rounded-full overflow-hidden">
                        <div class="bg-primary h-full transition-all" style="width: <?= $count_chat > 0 ? ($count_high / $count_chat) * 100 : 0 ?>%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-xs font-bold text-on-surface-variant mb-1">
                        <span>Akurasi Sedang (20% - 49%)</span>
                        <span class="text-secondary"><?= $count_med ?> Chat</span>
                    </div>
                    <div class="w-full bg-stone-200 h-2.5 rounded-full overflow-hidden">
                        <div class="bg-secondary h-full transition-all" style="width: <?= $count_chat > 0 ? ($count_med / $count_chat) * 100 : 0 ?>%"></div>
                    </div>
                </div>
                <div>
                    <div class="flex justify-between text-xs font-bold text-on-surface-variant mb-1">
                        <span>Akurasi Rendah (< 20%)</span>
                        <span class="text-error"><?= $count_low ?> Chat</span>
                    </div>
                    <div class="w-full bg-stone-200 h-2.5 rounded-full overflow-hidden">
                        <div class="bg-error h-full transition-all" style="width: <?= $count_chat > 0 ? ($count_low / $count_chat) * 100 : 0 ?>%"></div>
                    </div>
                </div>
            </div>
            <div class="mt-6 pt-4 border-t border-outline-variant/10 text-center">
                <a href="chat_logs.php" class="text-xs font-bold text-primary hover:underline">Lihat Semua Riwayat Chat →</a>
            </div>
        </div>

        <!-- Recent Chat Interactions (2 columns wide on large screens) -->
        <div class="lg:col-span-2 bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/20 shadow-sm flex flex-col justify-between">
            <div>
                <h3 class="font-headline text-lg font-bold text-on-surface flex items-center gap-2 mb-6">
                    <span class="material-symbols-outlined text-primary">history</span>
                    <span>Interaksi Chat Pengunjung Terbaru</span>
                </h3>

                <div class="space-y-4">
                    <?php while($row = $recent_chats->fetch_assoc()): 
                        $score = round($row['similarity_score'] * 100, 1);
                        $badge_class = "bg-primary-container text-on-primary-container";
                        if($score < 20) $badge_class = "bg-error-container text-on-error-container";
                        elseif($score < 50) $badge_class = "bg-stone-200 text-on-surface-variant";
                    ?>
                        <div class="p-4 rounded-xl border border-outline-variant/20 hover:bg-surface-container-low/30 transition-colors">
                            <div class="flex justify-between items-start mb-2">
                                <span class="text-[10px] font-bold text-on-surface-variant flex items-center gap-1">
                                    <span class="material-symbols-outlined text-xs">schedule</span>
                                    <?= date('d M H:i', strtotime($row['timestamp'])) ?>
                                </span>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded <?= $badge_class ?>">
                                    Akurasi: <?= $score ?>%
                                </span>
                            </div>
                            <p class="text-sm font-semibold text-on-surface italic mb-1">"<?= htmlspecialchars($row['user_input']) ?>"</p>
                            <p class="text-xs text-on-surface-variant line-clamp-1">Response: <?= htmlspecialchars($row['bot_response']) ?></p>
                        </div>
                    <?php endwhile; ?>
                    
                    <?php if($recent_chats->num_rows == 0): ?>
                        <div class="text-center py-10 text-on-surface-variant">
                            <span class="material-symbols-outlined text-4xl opacity-40">forum</span>
                            <p class="mt-2 text-sm">Belum ada aktivitas interaksi chatbot.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="mt-6 pt-4 border-t border-outline-variant/10 text-right">
                <a href="chat_logs.php" class="text-xs font-bold text-primary hover:underline">Lihat Detail Chat Logs →</a>
            </div>
        </div>

    </section>

    <!-- Quick Management Shortcut Links -->
    <section class="bg-surface-container-lowest p-6 rounded-xl border border-outline-variant/20 shadow-sm relative z-10 mb-8">
        <h3 class="font-headline text-lg font-bold text-on-surface flex items-center gap-2 mb-6">
            <span class="material-symbols-outlined text-primary">bolt</span>
            <span>Akses Cepat Pengelolaan Data</span>
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <a href="destinasi.php" class="p-4 bg-orange-50 hover:bg-orange-100/70 border border-orange-200/50 rounded-xl transition-all flex items-center gap-4 group">
                <div class="p-3 bg-orange-500 text-white rounded-lg group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined">add_to_photos</span>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-orange-950">Destinasi</h4>
                    <p class="text-[10px] text-orange-800 font-medium">Kelola Wisata</p>
                </div>
            </a>
            <a href="budaya.php" class="p-4 bg-amber-50 hover:bg-amber-100/70 border border-amber-200/50 rounded-xl transition-all flex items-center gap-4 group">
                <div class="p-3 bg-amber-500 text-white rounded-lg group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined">library_add</span>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-amber-950">Kebudayaan</h4>
                    <p class="text-[10px] text-amber-800 font-medium">Kelola Budaya</p>
                </div>
            </a>
            <a href="materi.php" class="p-4 bg-stone-50 hover:bg-stone-200/70 border border-stone-200 rounded-xl transition-all flex items-center gap-4 group">
                <div class="p-3 bg-stone-600 text-white rounded-lg group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined">note_add</span>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-stone-950">Materi Edukasi</h4>
                    <p class="text-[10px] text-stone-800 font-medium">Kelola Bacaan</p>
                </div>
            </a>
            <a href="knowledge_base.php" class="p-4 bg-rose-50 hover:bg-rose-100/70 border border-rose-200/50 rounded-xl transition-all flex items-center gap-4 group">
                <div class="p-3 bg-rose-500 text-white rounded-lg group-hover:scale-110 transition-transform">
                    <span class="material-symbols-outlined">psychology</span>
                </div>
                <div>
                    <h4 class="text-sm font-bold text-rose-950">Knowledge Base</h4>
                    <p class="text-[10px] text-rose-800 font-medium">Kelola Pengetahuan AI</p>
                </div>
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="mt-20 py-8 border-t border-outline-variant/10 text-center">
        <p class="font-label text-xs text-on-surface-variant tracking-widest uppercase">© <?= date('Y') ?> EduToraja NLP System.</p>
    </footer>
</main>
<?php
renderProfileModal($current_admin_id, $current_admin_foto, $admin_users);
?>
</body>
</html>
