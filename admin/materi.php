<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}

$result = $conn->query("SELECT * FROM materi ORDER BY id DESC");

// Ambil slug materi yang sudah ada
$materi_slugs = [];
$res_materi = $conn->query("SELECT slug FROM materi");
if ($res_materi) {
    while($row = $res_materi->fetch_assoc()) {
        $materi_slugs[] = $row['slug'];
    }
}

// Ambil destinasi & budaya yang BELUM ada materinya
$available_slugs = [];
$res_destinasi = $conn->query("SELECT nama, slug FROM destinasi");
if ($res_destinasi) {
    while($row = $res_destinasi->fetch_assoc()) {
        if (!in_array($row['slug'], $materi_slugs)) {
            $available_slugs[] = ['nama' => $row['nama'], 'slug' => $row['slug'], 'tipe' => 'Destinasi'];
        }
    }
}
$res_budaya = $conn->query("SELECT nama, slug FROM budaya");
if ($res_budaya) {
    while($row = $res_budaya->fetch_assoc()) {
        if (!in_array($row['slug'], $materi_slugs)) {
            $available_slugs[] = ['nama' => $row['nama'], 'slug' => $row['slug'], 'tipe' => 'Budaya'];
        }
    }
}

require_once 'profile_helper.php';
?>
<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Kelola Materi Edukasi | EduToraja</title>
    
    <link href="https://fonts.googleapis.com" rel="preconnect"/>
    <link crossorigin="" href="https://fonts.gstatic.com" rel="preconnect"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&amp;family=Noto+Serif:wght@400;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    
    <!-- Leaflet.js CSS & JS for Interactive Map in Admin -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
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
        .modal { display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; z-index: 101; width: 95%; max-width: 1500px; border-radius: 12px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); padding: 24px; max-height: 90vh; overflow-y: auto; }
        .modal.active, .modal-backdrop.active { display: block; }
        
        /* CKEditor Image Live Preview Styles */
        #add_preview_content .image, #edit_preview_content .image {
            display: table;
            clear: both;
            text-align: center;
            margin: 1em auto;
        }
        #add_preview_content .image img, #edit_preview_content .image img {
            max-width: 100%;
            height: auto;
        }
        #add_preview_content .image.image-style-align-left, #edit_preview_content .image.image-style-align-left {
            clear: none;
            float: left;
            margin-right: 1.5em;
        }
        #add_preview_content .image.image-style-align-right, #edit_preview_content .image.image-style-align-right {
            clear: none;
            float: right;
            margin-left: 1.5em;
        }
        #add_preview_content .image.image-style-align-center, #edit_preview_content .image.image-style-align-center {
            margin-left: auto;
            margin-right: auto;
        }

        /* Fix Tailwind Preflight stripping tags inside Live Preview to EXACTLY match materi.php */
        #add_preview_content p, #edit_preview_content p,
        #add_preview_content h1, #edit_preview_content h1,
        #add_preview_content h2, #edit_preview_content h2,
        #add_preview_content h4, #edit_preview_content h4,
        #add_preview_content h5, #edit_preview_content h5,
        #add_preview_content h6, #edit_preview_content h6,
        #add_preview_content blockquote, #edit_preview_content blockquote { 
            margin: 0; 
            padding: 0; 
        }
        
        #add_preview_content h1, #edit_preview_content h1 { font-size: 2em; font-weight: bold; }
        #add_preview_content h2, #edit_preview_content h2 { font-size: 1.5em; font-weight: bold; }
        #add_preview_content h3, #edit_preview_content h3 { font-size: 1.5rem; font-weight: 700; color: #3c2f2f; margin: 30px 0 15px 0; }
        #add_preview_content h4, #edit_preview_content h4 { font-size: 1em; font-weight: bold; }
        #add_preview_content h5, #edit_preview_content h5 { font-size: 0.83em; font-weight: bold; }
        #add_preview_content h6, #edit_preview_content h6 { font-size: 0.67em; font-weight: bold; }
        
        #add_preview_content ul, #edit_preview_content ul { list-style-type: disc; margin: 0 0 20px 20px; padding: 0; }
        #add_preview_content ol, #edit_preview_content ol { list-style-type: decimal; margin: 0 0 20px 20px; padding: 0; }
        #add_preview_content li, #edit_preview_content li { margin: 0 0 10px 0; padding: 0; display: list-item; }
        
        #add_preview_content strong, #edit_preview_content strong,
        #add_preview_content b, #edit_preview_content b { font-weight: 700; }
        #add_preview_content em, #edit_preview_content em,
        #add_preview_content i, #edit_preview_content i { font-style: italic; }
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
        <a class="bg-stone-50 text-orange-900 font-bold rounded-l-full ml-4 pl-4 py-3 flex items-center gap-3 transition-all duration-200" href="materi.php">
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
            <span class="material-symbols-outlined">add</span> Tambah Materi
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
            <h2 class="font-headline text-4xl text-on-surface font-bold">Kelola Materi Edukasi</h2>
            <p class="font-body text-on-surface-variant mt-2">Buat artikel materi pembelajaran detail untuk ditampilkan saat tombol "Pelajari Materi" diklik.</p>
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
                        <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Gambar Utama</th>
                        <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider">Judul Materi</th>
                        <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider w-4/12">Cuplikan Konten</th>
                        <th class="px-6 py-4 text-xs font-bold text-on-surface-variant uppercase tracking-wider text-right">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/20">
                    <?php while($row = $result->fetch_assoc()): 
                        $img_src = (strpos($row['gambar_url'], 'http') === 0) ? $row['gambar_url'] : '../' . $row['gambar_url'];
                    ?>
                    <tr class="hover:bg-surface-container-low/30 transition-colors">
                        <td class="px-6 py-5">
                            <img src="<?= htmlspecialchars($img_src) ?>" alt="Hero Image" class="w-24 h-16 object-cover rounded-lg bg-surface-container-highest">
                        </td>
                        <td class="px-6 py-5">
                            <p class="font-bold text-sm text-on-surface"><?= htmlspecialchars($row['judul']) ?></p>
                            <p class="text-xs text-on-surface-variant mt-1">Slug: <span class="font-mono bg-surface-variant px-1 rounded"><?= htmlspecialchars($row['slug']) ?></span></p>
                        </td>
                        <td class="px-6 py-5">
                            <p class="text-xs text-on-surface-variant line-clamp-2"><?= strip_tags($row['konten']) ?></p>
                        </td>
                        <td class="px-6 py-5 text-right space-x-2">
                            <button onclick="openEditModal(<?= $row['id'] ?>, `<?= htmlspecialchars($row['judul'], ENT_QUOTES) ?>`, `<?= htmlspecialchars($row['konten'], ENT_QUOTES) ?>`, `<?= htmlspecialchars($row['slug'], ENT_QUOTES) ?>`, `<?= htmlspecialchars($row['latitude'] ?? '', ENT_QUOTES) ?>`, `<?= htmlspecialchars($row['longitude'] ?? '', ENT_QUOTES) ?>`, `<?= htmlspecialchars($row['alamat_map'] ?? '', ENT_QUOTES) ?>`, `<?= htmlspecialchars($img_src, ENT_QUOTES) ?>`)" class="text-xs font-bold text-primary px-3 py-1.5 border border-primary/20 rounded-full hover:bg-primary hover:text-on-primary transition-all">Edit</button>
                            <a href="proses_materi.php?action=delete&id=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus materi ini?');" class="text-xs font-bold text-error px-3 py-1.5 border border-error/20 rounded-full hover:bg-error hover:text-on-error transition-all">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            <?php if($result->num_rows == 0): ?>
                <div class="text-center py-10 text-on-surface-variant">
                    <span class="material-symbols-outlined text-4xl opacity-50">menu_book</span>
                    <p class="mt-2 text-sm">Belum ada materi edukasi yang ditambahkan.</p>
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
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <div class="lg:col-span-5 border-r border-outline-variant/30 pr-6">
            <h3 class="font-headline text-2xl font-bold mb-4 text-on-surface">Tambah Materi Edukasi</h3>
            <p class="text-sm text-on-surface-variant mb-4">Materi yang diisi di sini akan muncul ketika tombol "Pelajari Materi" diklik di beranda user.</p>
            <form action="proses_materi.php?action=add" method="POST" enctype="multipart/form-data">
                <div class="grid grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-on-surface-variant mb-1">Judul Artikel Materi</label>
                        <input type="text" name="judul" id="add_judul_input" class="w-full rounded-lg border-outline-variant/50 focus:border-primary focus:ring-primary text-sm p-3 border" required placeholder="Contoh: Sejarah Londa" oninput="document.getElementById('add_preview_title').innerText = this.value || 'Judul Materi';">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-on-surface-variant mb-1">Pilih Objek (Destinasi/Budaya)</label>
                        <select name="slug" id="add_slug" class="w-full rounded-lg border-outline-variant/50 focus:border-primary focus:ring-primary text-sm p-3 border" required onchange="autoFillTitle()">
                            <option value="" disabled selected>-- Pilih Objek --</option>
                            <?php if(empty($available_slugs)): ?>
                                <option value="" disabled>Semua objek sudah memiliki materi</option>
                            <?php else: ?>
                                <?php foreach($available_slugs as $item): ?>
                                    <option value="<?= htmlspecialchars($item['slug']) ?>" data-nama="<?= htmlspecialchars($item['nama']) ?>">
                                        <?= htmlspecialchars($item['nama']) ?> (<?= $item['tipe'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-on-surface-variant mb-1">Upload Gambar Header Materi</label>
                    <input type="file" name="gambar" accept="image/*" class="w-full rounded-lg border-outline-variant/50 focus:border-primary focus:ring-primary text-sm p-2 border bg-surface-container-low" required onchange="previewImage(this, 'add_preview_img')">
                </div>
                <div class="mb-4 bg-surface-container-low p-4 rounded-lg border border-outline-variant/30">
                    <h4 class="font-bold text-sm text-on-surface mb-2 flex items-center gap-2"><span class="material-symbols-outlined text-primary">map</span> Pilih Peta Lokasi (Opsional)</h4>
                    <p class="text-xs text-on-surface-variant mb-4">Ketik nama tempat atau geser pin merah pada peta di bawah ini.</p>
                    
                    <div class="flex gap-2 mb-3">
                        <input type="text" id="add_search_map" class="flex-1 rounded-lg border-outline-variant/50 focus:border-primary focus:ring-primary text-sm p-2 border" placeholder="Ketik lokasi wisata (Cth: Buntu Burake)...">
                        <button type="button" onclick="searchLocation('add')" class="bg-primary text-on-primary px-4 rounded-lg text-sm font-bold hover:opacity-90">Cari</button>
                    </div>
                    
                    <div id="add_map_container" style="height: 250px; width: 100%; border-radius: 8px; z-index: 1;" class="mb-3 border border-outline-variant/50"></div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-3">
                        <div>
                            <label class="block text-xs font-medium text-on-surface-variant mb-1">Latitude</label>
                            <input type="text" name="latitude" id="add_latitude" class="w-full rounded-lg border-outline-variant/50 bg-surface-variant text-sm p-2 border" readonly>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-on-surface-variant mb-1">Longitude</label>
                            <input type="text" name="longitude" id="add_longitude" class="w-full rounded-lg border-outline-variant/50 bg-surface-variant text-sm p-2 border" readonly>
                        </div>
                    </div>
                        <div>
                            <label class="block text-xs font-medium text-on-surface-variant mb-1">Alamat Resmi</label>
                            <input type="text" name="alamat_map" id="add_alamat_map" class="w-full rounded-lg border-outline-variant/50 focus:border-primary focus:ring-primary text-sm p-2 border" placeholder="Alamat akan terisi otomatis..." oninput="document.getElementById('add_preview_address').innerText = this.value || 'Alamat tidak diketahui';">
                        </div>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-on-surface-variant mb-1">Konten Materi (Tinggal ketik seperti di Microsoft Word)</label>
                    <textarea name="konten" id="add_konten" class="w-full"></textarea>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeModals()" class="px-4 py-2 text-sm font-bold text-on-surface-variant hover:bg-surface-container-high rounded-full">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm font-bold bg-primary text-on-primary rounded-full hover:opacity-90">Simpan Materi</button>
                </div>
            </form>
        </div>
        
        <!-- Kolom Kanan: Live Preview Add -->
        <div class="lg:col-span-7 bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm border border-outline-variant/30 sticky top-0 h-fit">
            <div class="bg-surface-container-low p-3 border-b border-outline-variant/30 text-center flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-sm text-primary">visibility</span>
                <span class="text-xs font-bold text-on-surface-variant tracking-wider uppercase">Live Preview (Tampilan User)</span>
            </div>
            <div class="preview-container overflow-y-auto" style="max-height: 70vh; background: #faf6f0;">
                <div class="article-card" style="background: white; border-radius: 20px; overflow: hidden; margin: 20px; box-shadow: 0 10px 30px -5px rgba(0,0,0,0.05);">
                    <img id="add_preview_img" src="https://placehold.co/800x400?text=Pilih+Gambar+Materi" style="width: 100%; height: 250px; object-fit: cover;">
                    <div style="padding: 30px;">
                        <h1 id="add_preview_title" style="font-size: 2rem; font-weight: 800; margin-bottom: 20px; color: #3c2f2f; font-family: 'Outfit', sans-serif;">Judul Materi</h1>
                        <div id="add_preview_content" style="line-height: 1.8; font-size: 1.1rem; color: #7c6e6e; font-family: 'Outfit', sans-serif;">
                            Konten materi akan muncul di sini...
                        </div>
                        <div id="add_preview_map_section" style="display: none; margin-top: 30px;">
                            <hr style="margin: 30px 0 20px 0; border: 0; border-top: 1px solid #e2e8f0;">
                            <h3 style="color: #3c2f2f; margin-bottom: 15px; font-size: 1.5rem; font-weight: 700; font-family: 'Outfit', sans-serif; display: flex; align-items: center; gap: 10px;">
                                <span class="material-symbols-outlined" style="color: #934b19;">map</span> Peta Lokasi Wisata
                            </h3>
                            <div id="add_preview_map" style="width: 100%; height: 350px; border-radius: 15px; margin-bottom: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); z-index: 10;"></div>
                            <div style="display: flex; justify-content: space-between; align-items: center; background: #f1f5f9; padding: 15px 20px; border-radius: 12px; font-size: 0.95rem; flex-wrap: wrap; gap: 15px;">
                                <div>
                                    <span style="font-weight: 700; color: #3c2f2f; display: block; margin-bottom: 4px;"><span class="material-symbols-outlined" style="font-size: 14px; color: #934b19; vertical-align: middle;">location_on</span> Alamat Resmi</span>
                                    <span id="add_preview_address" style="color: #7c6e6e;">Alamat akan terisi otomatis...</span>
                                </div>
                                <a id="add_preview_route_btn" href="#" target="_blank" style="background: #934b19; color: white; padding: 10px 22px; border-radius: 30px; text-decoration: none; font-weight: 700; font-size: 0.85rem; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 6px rgba(245, 158, 11, 0.2);">
                                    <span class="material-symbols-outlined" style="font-size: 16px;">directions</span> Petunjuk Rute
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal" id="editModal">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <div class="lg:col-span-5 border-r border-outline-variant/30 pr-6">
            <h3 class="font-headline text-2xl font-bold mb-4 text-on-surface">Edit Materi Edukasi</h3>
            <form action="proses_materi.php?action=edit" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" id="edit_id">
                <div class="grid grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-on-surface-variant mb-1">Judul Artikel Materi</label>
                        <input type="text" name="judul" id="edit_judul" class="w-full rounded-lg border-outline-variant/50 focus:border-primary focus:ring-primary text-sm p-3 border" required oninput="document.getElementById('edit_preview_title').innerText = this.value || 'Judul Materi';">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-on-surface-variant mb-1">Slug (Tidak dapat diubah)</label>
                        <input type="text" name="slug" id="edit_slug" class="w-full rounded-lg border-outline-variant/50 bg-surface-variant cursor-not-allowed text-sm p-3 border" required readonly>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-on-surface-variant mb-1">Ganti Gambar Header (Kosongkan jika tidak diganti)</label>
                    <input type="file" name="gambar" accept="image/*" class="w-full rounded-lg border-outline-variant/50 focus:border-primary focus:ring-primary text-sm p-2 border bg-surface-container-low" onchange="previewImage(this, 'edit_preview_img')">
                </div>
                <div class="mb-4 bg-surface-container-low p-4 rounded-lg border border-outline-variant/30">
                    <h4 class="font-bold text-sm text-on-surface mb-2 flex items-center gap-2"><span class="material-symbols-outlined text-primary">map</span> Pilih Peta Lokasi (Opsional)</h4>
                    <p class="text-xs text-on-surface-variant mb-4">Ketik nama tempat atau geser pin merah pada peta di bawah ini.</p>
                    
                    <div class="flex gap-2 mb-3">
                        <input type="text" id="edit_search_map" class="flex-1 rounded-lg border-outline-variant/50 focus:border-primary focus:ring-primary text-sm p-2 border" placeholder="Ketik lokasi wisata (Cth: Buntu Burake)...">
                        <button type="button" onclick="searchLocation('edit')" class="bg-primary text-on-primary px-4 rounded-lg text-sm font-bold hover:opacity-90">Cari</button>
                    </div>
                    
                    <div id="edit_map_container" style="height: 250px; width: 100%; border-radius: 8px; z-index: 1;" class="mb-3 border border-outline-variant/50"></div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-3">
                        <div>
                            <label class="block text-xs font-medium text-on-surface-variant mb-1">Latitude</label>
                            <input type="text" name="latitude" id="edit_latitude" class="w-full rounded-lg border-outline-variant/50 bg-surface-variant text-sm p-2 border" readonly>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-on-surface-variant mb-1">Longitude</label>
                            <input type="text" name="longitude" id="edit_longitude" class="w-full rounded-lg border-outline-variant/50 bg-surface-variant text-sm p-2 border" readonly>
                        </div>
                    </div>
                        <div>
                            <label class="block text-xs font-medium text-on-surface-variant mb-1">Alamat Resmi</label>
                            <input type="text" name="alamat_map" id="edit_alamat_map" class="w-full rounded-lg border-outline-variant/50 focus:border-primary focus:ring-primary text-sm p-2 border" placeholder="Alamat akan terisi otomatis..." oninput="document.getElementById('edit_preview_address').innerText = this.value || 'Alamat tidak diketahui';">
                        </div>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-medium text-on-surface-variant mb-1">Konten Materi (Tinggal ketik seperti di Microsoft Word)</label>
                    <textarea name="konten" id="edit_konten" class="w-full"></textarea>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" onclick="closeModals()" class="px-4 py-2 text-sm font-bold text-on-surface-variant hover:bg-surface-container-high rounded-full">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm font-bold bg-primary text-on-primary rounded-full hover:opacity-90">Simpan Perubahan</button>
                </div>
            </form>
        </div>
        
        <!-- Kolom Kanan: Live Preview Edit -->
        <div class="lg:col-span-7 bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm border border-outline-variant/30 sticky top-0 h-fit">
            <div class="bg-surface-container-low p-3 border-b border-outline-variant/30 text-center flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-sm text-primary">visibility</span>
                <span class="text-xs font-bold text-on-surface-variant tracking-wider uppercase">Live Preview (Tampilan User)</span>
            </div>
            <div class="preview-container overflow-y-auto" style="max-height: 70vh; background: #faf6f0;">
                <div class="article-card" style="background: white; border-radius: 20px; overflow: hidden; margin: 20px; box-shadow: 0 10px 30px -5px rgba(0,0,0,0.05);">
                    <img id="edit_preview_img" src="https://placehold.co/800x400?text=Gambar+Materi" style="width: 100%; height: 250px; object-fit: cover;">
                    <div style="padding: 30px;">
                        <h1 id="edit_preview_title" style="font-size: 2rem; font-weight: 800; margin-bottom: 20px; color: #3c2f2f; font-family: 'Outfit', sans-serif;">Judul Materi</h1>
                        <div id="edit_preview_content" style="line-height: 1.8; font-size: 1.1rem; color: #7c6e6e; font-family: 'Outfit', sans-serif;">
                            Konten materi akan muncul di sini...
                        </div>
                        <div id="edit_preview_map_section" style="display: none; margin-top: 30px;">
                            <hr style="margin: 30px 0 20px 0; border: 0; border-top: 1px solid #e2e8f0;">
                            <h3 style="color: #3c2f2f; margin-bottom: 15px; font-size: 1.5rem; font-weight: 700; font-family: 'Outfit', sans-serif; display: flex; align-items: center; gap: 10px;">
                                <span class="material-symbols-outlined" style="color: #934b19;">map</span> Peta Lokasi Wisata
                            </h3>
                            <div id="edit_preview_map" style="width: 100%; height: 350px; border-radius: 15px; margin-bottom: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); z-index: 10;"></div>
                            <div style="display: flex; justify-content: space-between; align-items: center; background: #f1f5f9; padding: 15px 20px; border-radius: 12px; font-size: 0.95rem; flex-wrap: wrap; gap: 15px;">
                                <div>
                                    <span style="font-weight: 700; color: #3c2f2f; display: block; margin-bottom: 4px;"><span class="material-symbols-outlined" style="font-size: 14px; color: #934b19; vertical-align: middle;">location_on</span> Alamat Resmi</span>
                                    <span id="edit_preview_address" style="color: #7c6e6e;">Alamat akan terisi otomatis...</span>
                                </div>
                                <a id="edit_preview_route_btn" href="#" target="_blank" style="background: #934b19; color: white; padding: 10px 22px; border-radius: 30px; text-decoration: none; font-weight: 700; font-size: 0.85rem; display: flex; align-items: center; gap: 8px; box-shadow: 0 4px 6px rgba(245, 158, 11, 0.2);">
                                    <span class="material-symbols-outlined" style="font-size: 16px;">directions</span> Petunjuk Rute
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CKEditor 5 Super Build -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/super-build/ckeditor.js"></script>
<style>
    /* Styling for CKEditor inside Tailwind */
    .ck-editor__editable_inline {
        min-height: 250px;
        font-family: 'Inter', sans-serif !important;
        font-size: 14px;
        color: #1b1c1a;
    }
</style>

<script>
    let addEditor, editEditor;

    // Image Preview Helper
    function previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(previewId).src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Custom CKEditor Upload Adapter
    class MyUploadAdapter {
        constructor(loader) {
            this.loader = loader;
        }
        upload() {
            return this.loader.file
                .then(file => new Promise((resolve, reject) => {
                    const data = new FormData();
                    data.append('upload', file);
                    fetch('upload_editor_image.php', {
                        method: 'POST',
                        body: data
                    })
                    .then(response => response.json())
                    .then(response => {
                        if (response.error) {
                            reject(response.error.message);
                        } else {
                            // Ciptakan absolute path ke project root (misal: /edukasi_pariwisata/uploads/...)
                            let basePath = window.location.pathname.substring(0, window.location.pathname.indexOf('/admin/'));
                            resolve({ default: basePath + '/' + response.url });
                        }
                    })
                    .catch(err => reject(err));
                }));
        }
        abort() {}
    }

    function MyCustomUploadAdapterPlugin(editor) {
        editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
            return new MyUploadAdapter(loader);
        };
    }

    const editorConfig = {
        plugins: [
            'Essentials', 'Paragraph', 'Bold', 'Italic', 'Heading', 'Link', 'List',
            'Image', 'ImageUpload', 'ImageToolbar', 'ImageStyle', 'ImageResize', 'ImageCaption',
            'Alignment', 'BlockQuote', 'Table', 'TableToolbar'
        ],
        toolbar: [
            'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|',
            'alignment', '|', 'imageUpload', 'blockQuote', 'insertTable', '|', 'undo', 'redo'
        ],
        image: {
            toolbar: [
                'imageStyle:inline', 'imageStyle:block', 'imageStyle:side', '|',
                'imageStyle:alignLeft', 'imageStyle:alignRight', 'imageStyle:alignCenter', '|',
                'toggleImageCaption', 'imageTextAlternative'
            ],
            resizeOptions: [
                { name: 'resizeImage:original', label: 'Original', value: null },
                { name: 'resizeImage:50', label: '50%', value: '50' },
                { name: 'resizeImage:75', label: '75%', value: '75' }
            ]
        },
        extraPlugins: [ MyCustomUploadAdapterPlugin ]
    };

    // Initialize CKEditor for Add Modal
    CKEDITOR.ClassicEditor
        .create(document.querySelector('#add_konten'), editorConfig)
        .then(editor => { 
            addEditor = editor; 
            editor.model.document.on('change:data', () => {
                document.getElementById('add_preview_content').innerHTML = editor.getData() || 'Konten materi akan muncul di sini...';
            });
        })
        .catch(error => { console.error(error); });

    // Initialize CKEditor for Edit Modal
    CKEDITOR.ClassicEditor
        .create(document.querySelector('#edit_konten'), editorConfig)
        .then(editor => { 
            editEditor = editor; 
            editor.model.document.on('change:data', () => {
                document.getElementById('edit_preview_content').innerHTML = editor.getData() || 'Konten materi akan muncul di sini...';
            });
        })
        .catch(error => { console.error(error); });

    let addMap, editMap, addMarker, editMarker;
    let addPreviewMap, editPreviewMap, addPreviewMarker, editPreviewMarker;
    const defaultLat = -2.9774; // Rantepao center
    const defaultLng = 119.8979;

    var customIcon = L.divIcon({
        html: '<span class="material-symbols-outlined" style="color: #d97706; font-size: 36px; text-shadow: 0 2px 5px rgba(0,0,0,0.3);">location_on</span>',
        iconSize: [36, 36],
        iconAnchor: [18, 36],
        popupAnchor: [0, -36],
        className: 'custom-div-icon'
    });

    function initPreviewMap(type, lat, lng, title, address) {
        let centerLat = lat ? parseFloat(lat) : defaultLat;
        let centerLng = lng ? parseFloat(lng) : defaultLng;

        if (type === 'add') {
            if (!addPreviewMap) {
                addPreviewMap = L.map('add_preview_map', { zoomControl: false }).setView([centerLat, centerLng], 14);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(addPreviewMap);
                addPreviewMarker = L.marker([centerLat, centerLng], { icon: customIcon }).addTo(addPreviewMap);
            } else {
                addPreviewMap.setView([centerLat, centerLng], 14);
                addPreviewMarker.setLatLng([centerLat, centerLng]);
            }
            if(title || address) {
                let pTitle = title || document.getElementById('add_judul_input').value || 'Lokasi Wisata';
                let pAddress = address || 'Alamat tidak diketahui';
                addPreviewMarker.bindPopup(`<div style="font-family: 'Outfit', sans-serif; font-size: 0.95rem;"><b>${pTitle}</b><br><span style="color: #64748b;">${pAddress}</span></div>`).openPopup();
            }
            setTimeout(() => { addPreviewMap.invalidateSize(); }, 300);
        } else {
            if (!editPreviewMap) {
                editPreviewMap = L.map('edit_preview_map', { zoomControl: false }).setView([centerLat, centerLng], 14);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(editPreviewMap);
                editPreviewMarker = L.marker([centerLat, centerLng], { icon: customIcon }).addTo(editPreviewMap);
            } else {
                editPreviewMap.setView([centerLat, centerLng], 14);
                editPreviewMarker.setLatLng([centerLat, centerLng]);
            }
            if(title || address) {
                let pTitle = title || document.getElementById('edit_judul').value || 'Lokasi Wisata';
                let pAddress = address || 'Alamat tidak diketahui';
                editPreviewMarker.bindPopup(`<div style="font-family: 'Outfit', sans-serif; font-size: 0.95rem;"><b>${pTitle}</b><br><span style="color: #64748b;">${pAddress}</span></div>`).openPopup();
            }
            setTimeout(() => { editPreviewMap.invalidateSize(); }, 300);
        }
    }

    function initAddMap() {
        if (!addMap) {
            addMap = L.map('add_map_container').setView([defaultLat, defaultLng], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(addMap);
            addMarker = L.marker([defaultLat, defaultLng], {draggable: true}).addTo(addMap);
            
            addMarker.on('dragend', function(e){
                updateMapFields('add', e.target.getLatLng());
            });
            addMap.on('click', function(e) {
                addMarker.setLatLng(e.latlng);
                updateMapFields('add', e.latlng);
            });
        }
        setTimeout(() => { addMap.invalidateSize(); }, 200);
    }

    function initEditMap(lat, lng) {
        let centerLat = lat ? parseFloat(lat) : defaultLat;
        let centerLng = lng ? parseFloat(lng) : defaultLng;
        
        if (!editMap) {
            editMap = L.map('edit_map_container').setView([centerLat, centerLng], 12);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(editMap);
            editMarker = L.marker([centerLat, centerLng], {draggable: true}).addTo(editMap);
            
            editMarker.on('dragend', function(e){
                updateMapFields('edit', e.target.getLatLng());
            });
            editMap.on('click', function(e) {
                editMarker.setLatLng(e.latlng);
                updateMapFields('edit', e.latlng);
            });
        } else {
            editMap.setView([centerLat, centerLng], 12);
            editMarker.setLatLng([centerLat, centerLng]);
        }
        setTimeout(() => { editMap.invalidateSize(); }, 200);
    }

    function updateMapFields(type, latlng, address = null) {
        document.getElementById(type + '_latitude').value = latlng.lat;
        document.getElementById(type + '_longitude').value = latlng.lng;
        document.getElementById(type + '_preview_map_section').style.display = 'block';
        
        document.getElementById(type + '_preview_route_btn').href = `https://www.google.com/maps/dir/?api=1&destination=${latlng.lat},${latlng.lng}`;

        if (address) {
            document.getElementById(type + '_alamat_map').value = address;
            document.getElementById(type + '_preview_address').innerText = address;
            initPreviewMap(type, latlng.lat, latlng.lng, null, address);
        } else {
            // auto-fetch address
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${latlng.lat}&lon=${latlng.lng}`)
                .then(res => res.json())
                .then(data => {
                    let addr = "Alamat tidak ditemukan";
                    if (data && data.display_name) {
                        addr = data.display_name;
                    }
                    document.getElementById(type + '_alamat_map').value = addr;
                    document.getElementById(type + '_preview_address').innerText = addr;
                    initPreviewMap(type, latlng.lat, latlng.lng, null, addr);
                });
        }
    }

    function searchLocation(type) {
        let query = document.getElementById(type + '_search_map').value;
        if (!query) return;
        
        let searchBtn = document.activeElement;
        searchBtn.innerText = "Mencari...";
        
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                searchBtn.innerText = "Cari";
                if (data && data.length > 0) {
                    let lat = parseFloat(data[0].lat);
                    let lng = parseFloat(data[0].lon);
                    
                    document.getElementById(type + '_latitude').value = lat;
                    document.getElementById(type + '_longitude').value = lng;
                    document.getElementById(type + '_alamat_map').value = data[0].display_name;
                    
                    if (type === 'add') {
                        addMap.setView([lat, lng], 14);
                        addMarker.setLatLng([lat, lng]);
                        updateMapFields('add', {lat: lat, lng: lng}, data[0].display_name);
                    } else {
                        editMap.setView([lat, lng], 14);
                        editMarker.setLatLng([lat, lng]);
                        updateMapFields('edit', {lat: lat, lng: lng}, data[0].display_name);
                    }
                } else {
                    alert("Lokasi tidak ditemukan. Coba kata kunci lain.");
                }
            })
            .catch(() => { searchBtn.innerText = "Cari"; });
    }

    function openModal(id) {
        document.getElementById('backdrop').classList.add('active');
        document.getElementById(id).classList.add('active');
        
        if(id === 'addModal') {
            initAddMap();
        }
    }

    // Auto fill title when selecting an object
    function autoFillTitle() {
        let select = document.getElementById('add_slug');
        let selectedOption = select.options[select.selectedIndex];
        let titleInput = document.getElementById('add_judul_input');
        if (selectedOption && selectedOption.value !== "") {
            let name = selectedOption.getAttribute('data-nama');
            if (titleInput.value === "") {
                titleInput.value = name;
                document.getElementById('add_preview_title').innerText = name;
            }
        }
    }

    function closeModals() {
        document.getElementById('backdrop').classList.remove('active');
        document.querySelectorAll('.modal').forEach(m => m.classList.remove('active'));
    }

    function openEditModal(id, judul, konten, slug, latitude, longitude, alamat_map, gambar_url) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_judul').value = judul;
        document.getElementById('edit_slug').value = slug;
        document.getElementById('edit_latitude').value = latitude;
        document.getElementById('edit_longitude').value = longitude;
        document.getElementById('edit_alamat_map').value = alamat_map;
        
        // Populate Preview
        document.getElementById('edit_preview_title').innerText = judul || 'Judul Materi';
        if (gambar_url) {
            document.getElementById('edit_preview_img').src = gambar_url;
        }
        if (latitude && longitude) {
            document.getElementById('edit_preview_map_section').style.display = 'block';
            document.getElementById('edit_preview_address').innerText = alamat_map || 'Alamat tidak diketahui';
            document.getElementById('edit_preview_route_btn').href = `https://www.google.com/maps/dir/?api=1&destination=${latitude},${longitude}`;
            setTimeout(() => {
                initPreviewMap('edit', latitude, longitude, judul, alamat_map);
            }, 300);
        } else {
            document.getElementById('edit_preview_map_section').style.display = 'none';
        }
        
        // Set data to CKEditor
        if (editEditor) {
            editEditor.setData(konten);
        } else {
            document.getElementById('edit_konten').value = konten;
        }
        
        openModal('editModal');
        initEditMap(latitude, longitude);
    }
</script>

<?php
renderProfileModal($current_admin_id, $current_admin_foto, $admin_users);
?>
</body>
</html>
