<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}

$result = $conn->query("SELECT * FROM destinasi ORDER BY id DESC");
$count_destinasi = $result->num_rows;
require_once 'profile_helper.php';
?>
<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Kelola Destinasi | EduToraja</title>
    
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
        <a class="bg-stone-50 text-orange-900 font-bold rounded-l-full ml-4 pl-4 py-3 flex items-center gap-3 transition-all duration-200" href="destinasi.php">
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
            <span class="text-sm font-medium">Lihat Website</span>
        </a>
    </nav>
    <div class="px-6 mt-auto">
        <button onclick="openModal('addModal')" class="w-full py-3 px-4 bg-primary text-on-primary rounded-full flex items-center justify-center gap-2 text-sm font-medium hover:opacity-90 transition-all duration-200">
            <span class="material-symbols-outlined">add</span> Tambah Wisata
        </button>
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
        <div>
            <h2 class="font-headline text-4xl text-on-surface font-bold">Kelola Objek Wisata</h2>
            <p class="font-body text-on-surface-variant mt-2">Tambahkan destinasi wisata yang akan muncul di beranda pengunjung.</p>
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

    <?php if(isset($_SESSION['msg'])): ?>
        <div id="alert-msg" class="bg-secondary-container text-on-secondary-container p-4 rounded-lg mb-8 relative z-10 flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined">check_circle</span>
                <p class="text-sm font-medium"><?= $_SESSION['msg'] ?></p>
            </div>
            <button onclick="document.getElementById('alert-msg').style.display='none'" class="text-on-secondary-container hover:bg-black/10 p-1 rounded-full transition-colors flex items-center justify-center">
                <span class="material-symbols-outlined text-sm">close</span>
            </button>
        </div>
        <?php unset($_SESSION['msg']); ?>
    <?php endif; ?>

    <!-- Table Section -->
    <section class="relative z-10">
        <div class="bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm border border-outline-variant/30">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-surface-container-low border-b border-outline-variant/30">
                        <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Gambar</th>
                        <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Nama Destinasi</th>
                        <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider w-5/12">Deskripsi Singkat</th>
                        <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20">
                    <?php while($row = $result->fetch_assoc()): 
                        $img_src = (strpos($row['gambar_url'], 'http') === 0) ? $row['gambar_url'] : '../' . $row['gambar_url'];
                    ?>
                    <tr class="hover:bg-surface-container-low/30 transition-colors">
                        <td class="px-6 py-5">
                            <img src="<?= htmlspecialchars($img_src) ?>" alt="Thumbnail" class="w-16 h-16 object-cover rounded-lg bg-surface-container-highest">
                        </td>
                        <td class="px-6 py-5">
                            <p class="font-bold text-sm text-on-surface"><?= htmlspecialchars($row['nama']) ?></p>
                            <p class="text-xs text-on-surface-variant mt-1">Slug: <?= htmlspecialchars($row['slug']) ?></p>
                        </td>
                        <td class="px-6 py-5">
                            <p class="text-sm text-on-surface-variant line-clamp-2"><?= htmlspecialchars($row['deskripsi']) ?></p>
                        </td>
                        <td class="px-6 py-5 text-right space-x-2">
                            <button onclick="openEditModal(<?= $row['id'] ?>, `<?= htmlspecialchars($row['nama'], ENT_QUOTES) ?>`, `<?= htmlspecialchars($row['deskripsi'], ENT_QUOTES) ?>`, `<?= htmlspecialchars($row['gambar_url'], ENT_QUOTES) ?>`, `<?= htmlspecialchars($row['slug'], ENT_QUOTES) ?>`)" class="text-xs font-bold text-primary px-3 py-1.5 border border-primary/20 rounded-full hover:bg-primary hover:text-on-primary transition-all">Edit</button>
                            <a href="proses_destinasi.php?action=delete&id=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus wisata ini dari daftar?');" class="text-xs font-bold text-error px-3 py-1.5 border border-error/20 rounded-full hover:bg-error hover:text-on-error transition-all">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php if($result->num_rows == 0): ?>
                <div class="text-center py-10 text-on-surface-variant">
                    <span class="material-symbols-outlined text-4xl opacity-50">landscape_blank</span>
                    <p class="mt-2 text-sm">Belum ada objek wisata yang ditambahkan.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

</main>

<button onclick="openModal('addModal')" class="fixed bottom-10 right-10 bg-primary text-on-primary w-14 h-14 rounded-full shadow-lg flex items-center justify-center hover:scale-105 active:scale-95 transition-all z-50">
    <span class="material-symbols-outlined">add</span>
</button>

<div class="modal-backdrop" id="backdrop" onclick="closeModals()"></div>

<!-- Add Modal -->
<div class="modal" id="addModal">
    <h3 class="font-headline text-2xl font-bold mb-4 text-on-surface">Tambah Destinasi</h3>
    <form action="proses_destinasi.php?action=add" method="POST" enctype="multipart/form-data">
        <div class="mb-4">
            <label class="block text-sm font-medium text-on-surface-variant mb-1">Nama Destinasi</label>
            <input type="text" name="nama" class="w-full rounded-lg border-outline-variant/50 focus:border-primary focus:ring-primary text-sm p-3 border" required placeholder="Contoh: Kete Kesu">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-on-surface-variant mb-1">Slug (Kata Kunci URL, tanpa spasi)</label>
            <input type="text" name="slug" class="w-full rounded-lg border-outline-variant/50 focus:border-primary focus:ring-primary text-sm p-3 border" required placeholder="Contoh: kete-kesu">
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-on-surface-variant mb-1">Upload Gambar dari Laptop</label>
            <input type="file" name="gambar" accept="image/*" class="w-full rounded-lg border-outline-variant/50 focus:border-primary focus:ring-primary text-sm p-2 border bg-surface-container-low" required>
        </div>
        <div class="mb-6">
            <label class="block text-sm font-medium text-on-surface-variant mb-1">Deskripsi Singkat</label>
            <textarea name="deskripsi" rows="3" class="w-full rounded-lg border-outline-variant/50 focus:border-primary focus:ring-primary text-sm p-3 border" required></textarea>
        </div>
        <div class="flex justify-end gap-3">
            <button type="button" onclick="closeModals()" class="px-4 py-2 text-sm font-bold text-on-surface-variant hover:bg-surface-container-high rounded-full">Batal</button>
            <button type="submit" class="px-4 py-2 text-sm font-bold bg-primary text-on-primary rounded-full hover:opacity-90">Simpan Data</button>
        </div>
    </form>
</div>

<!-- Edit Modal -->
<div class="modal" id="editModal">
    <h3 class="font-headline text-2xl font-bold mb-4 text-on-surface">Edit Destinasi</h3>
    <form action="proses_destinasi.php?action=edit" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" id="edit_id">
        <div class="mb-4">
            <label class="block text-sm font-medium text-on-surface-variant mb-1">Nama Destinasi</label>
            <input type="text" name="nama" id="edit_nama" class="w-full rounded-lg border-outline-variant/50 focus:border-primary focus:ring-primary text-sm p-3 border" required>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-on-surface-variant mb-1">Slug</label>
            <input type="text" name="slug" id="edit_slug" class="w-full rounded-lg border-outline-variant/50 focus:border-primary focus:ring-primary text-sm p-3 border" required>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-on-surface-variant mb-1">Ganti Gambar (Kosongkan jika tidak diganti)</label>
            <input type="file" name="gambar" accept="image/*" class="w-full rounded-lg border-outline-variant/50 focus:border-primary focus:ring-primary text-sm p-2 border bg-surface-container-low">
        </div>
        <div class="mb-6">
            <label class="block text-sm font-medium text-on-surface-variant mb-1">Deskripsi Singkat</label>
            <textarea name="deskripsi" id="edit_deskripsi" rows="3" class="w-full rounded-lg border-outline-variant/50 focus:border-primary focus:ring-primary text-sm p-3 border" required></textarea>
        </div>
        <div class="flex justify-end gap-3">
            <button type="button" onclick="closeModals()" class="px-4 py-2 text-sm font-bold text-on-surface-variant hover:bg-surface-container-high rounded-full">Batal</button>
            <button type="submit" class="px-4 py-2 text-sm font-bold bg-primary text-on-primary rounded-full hover:opacity-90">Simpan Perubahan</button>
        </div>
    </form>
</div>

<script>
    function openModal(id) {
        document.getElementById('backdrop').classList.add('active');
        document.getElementById(id).classList.add('active');
    }

    function closeModals() {
        document.getElementById('backdrop').classList.remove('active');
        document.querySelectorAll('.modal').forEach(m => m.classList.remove('active'));
    }

    function openEditModal(id, nama, deskripsi, gambar, slug) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_nama').value = nama;
        document.getElementById('edit_deskripsi').value = deskripsi;
        document.getElementById('edit_slug').value = slug;
        openModal('editModal');
    }
</script>

<?php
renderProfileModal($current_admin_id, $current_admin_foto, $admin_users);
?>
</body>
</html>
