<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}

if (isset($_GET['action'])) {
    if ($_GET['action'] == 'delete' && isset($_GET['id'])) {
        $id = (int)$_GET['id'];
        $conn->query("DELETE FROM chat_logs WHERE id = $id");
        $_SESSION['msg'] = "Satu log percakapan berhasil dihapus.";
        header("Location: chat_logs.php");
        exit;
    }
    if ($_GET['action'] == 'delete_all') {
        $conn->query("TRUNCATE TABLE chat_logs");
        $_SESSION['msg'] = "Semua riwayat percakapan berhasil dihapus.";
        header("Location: chat_logs.php");
        exit;
    }
}

$result = $conn->query("SELECT * FROM chat_logs ORDER BY timestamp DESC");
require_once 'profile_helper.php';
?>
<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Chat Logs | EduToraja</title>
    
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
        
        .modal-backdrop { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; backdrop-filter: blur(2px); }
        .modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; z-index: 101; width: 90%; max-width: 600px; border-radius: 12px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); padding: 24px; max-height: 90vh; overflow-y: auto; }
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
        <a class="text-stone-500 pl-8 py-3 flex items-center gap-3 hover:bg-stone-200 rounded-lg transition-all duration-200" href="dashboard.php">
            <span class="material-symbols-outlined">dashboard</span>
            <span class="text-sm font-medium">Dashboard</span>
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
        <a class="bg-stone-50 text-orange-900 font-bold rounded-l-full ml-4 pl-4 py-3 flex items-center gap-3 transition-all duration-200" href="chat_logs.php">
            <span class="material-symbols-outlined">forum</span>
            <span class="text-sm">Chat Logs</span>
        </a>
        <a class="text-stone-500 pl-8 py-3 flex items-center gap-3 hover:bg-stone-200 rounded-lg transition-all duration-200" href="../index.php" target="_blank">
            <span class="material-symbols-outlined">public</span>
            <span class="text-sm">Lihat Website</span>
        </a>
    </nav>
    <div class="px-6 mt-auto">
        <div class="mt-6 pt-6 border-t border-outline-variant/20">
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

    <header class="flex justify-between items-end mb-12 relative z-10">
        <div class="flex items-center gap-6">
            <div>
                <h2 class="font-headline text-4xl text-on-surface font-bold">Riwayat Percakapan AI</h2>
                <p class="font-body text-on-surface-variant mt-2">Pantau seberapa pintar AI menanggapi kueri pengunjung melalui analisis TF-IDF.</p>
            </div>
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

    <!-- Table Section -->
    <section class="relative z-10">
        <?php if(isset($_SESSION['msg'])): ?>
            <div id="alert-msg" class="bg-secondary-container text-on-secondary-container p-4 rounded-lg mb-8 flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined">check_circle</span>
                    <p class="text-sm font-medium"><?= htmlspecialchars($_SESSION['msg']) ?></p>
                </div>
                <button onclick="document.getElementById('alert-msg').style.display='none'" class="text-on-secondary-container hover:bg-black/10 p-1 rounded-full transition-colors flex items-center justify-center">
                    <span class="material-symbols-outlined text-sm">close</span>
                </button>
            </div>
            <?php unset($_SESSION['msg']); ?>
        <?php endif; ?>

        <div class="bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm border border-outline-variant/30">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low border-b border-outline-variant/30">
                        <th class="px-8 py-5 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Waktu Kueri</th>
                        <th class="px-8 py-5 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Pertanyaan Pengguna</th>
                        <th class="px-8 py-5 text-xs font-bold text-on-surface-variant uppercase tracking-wider w-5/12">Respons Bot (Bot Reply)</th>
                        <th class="px-8 py-5 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Akurasi TF-IDF</th>
                        <th class="px-8 py-5 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20">
                    <?php while($row = $result->fetch_assoc()): 
                        $score = round($row['similarity_score'] * 100, 2);
                        // Tentukan style score mirip dengan template error/success states
                        $score_style = "bg-primary-container text-on-primary-container"; // High score (orange)
                        if($score < 20) $score_style = "bg-error-container text-on-error-container"; // Bad
                        elseif($score < 50) $score_style = "bg-surface-variant text-on-surface-variant"; // Mid
                    ?>
                    <tr class="hover:bg-surface-container-low/30 transition-colors">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-secondary-fixed flex items-center justify-center text-on-secondary-fixed font-bold text-xs">
                                    <span class="material-symbols-outlined text-sm">schedule</span>
                                </div>
                                <div>
                                    <p class="font-bold text-sm text-on-surface"><?= date('d M Y', strtotime($row['timestamp'])) ?></p>
                                    <p class="text-xs text-on-surface-variant"><?= date('H:i', strtotime($row['timestamp'])) ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <p class="text-sm font-medium text-on-surface italic">"<?= htmlspecialchars($row['user_input']) ?>"</p>
                        </td>
                        <td class="px-8 py-6">
                            <p class="text-sm text-on-surface-variant line-clamp-2"><?= htmlspecialchars($row['bot_response']) ?></p>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-xs font-bold px-3 py-1.5 rounded-md <?= $score_style ?>">
                                <?= $score ?>%
                            </span>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <a href="chat_logs.php?action=delete&id=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus percakapan ini?');" class="text-xs font-bold text-error px-3 py-1.5 border border-error/20 rounded-full hover:bg-error hover:text-white transition-all">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            
            <?php if($result->num_rows == 0): ?>
                <div class="text-center py-10 text-on-surface-variant">
                    <span class="material-symbols-outlined text-4xl opacity-50">forum</span>
                    <p class="mt-2 text-sm">Belum ada aktivitas chat terekam.</p>
                </div>
            <?php endif; ?>
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
