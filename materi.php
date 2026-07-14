<?php
require_once 'config.php';
$id = isset($_GET['id']) ? $_GET['id'] : '';

$selected_materi = null;
$has_map = false;
$selected_coords = null;

if (!empty($id)) {
    $stmt = $conn->prepare("SELECT * FROM materi WHERE slug = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $selected_materi = [
            'title' => $row['judul'],
            'image' => (strpos($row['gambar_url'], 'http') === 0) ? $row['gambar_url'] : $row['gambar_url'],
            'content' => $row['konten']
        ];

        if (!empty($row['latitude']) && !empty($row['longitude'])) {
            $has_map = true;
            $selected_coords = [
                'lat' => $row['latitude'],
                'lng' => $row['longitude'],
                'address' => !empty($row['alamat_map']) ? $row['alamat_map'] : 'Lokasi Wisata'
            ];
        }
    }
}

// Dynamic Theme based on slug
$themes = [
    'londa' => [
        'bg' => 'linear-gradient(135deg, #1f2937 0%, #0f172a 100%)',
        'icons' => '<i class="fa-solid fa-mountain" style="font-size: 15rem; color: rgba(255,255,255,0.03); position: fixed; left: -5%; top: 20%; transform: rotate(-15deg); z-index: -1;"></i><i class="fa-solid fa-skull" style="font-size: 8rem; color: rgba(255,255,255,0.02); position: fixed; right: 5%; top: 65%; transform: rotate(10deg); z-index: -1;"></i><i class="fa-solid fa-dungeon" style="font-size: 18rem; color: rgba(255,255,255,0.03); position: fixed; right: -5%; top: 10%; transform: rotate(15deg); z-index: -1;"></i><i class="fa-solid fa-cloud-moon" style="font-size: 10rem; color: rgba(255,255,255,0.02); position: fixed; left: 10%; top: 75%; transform: rotate(-5deg); z-index: -1;"></i>'
    ],
    'kete_kesu' => [
        'bg' => 'linear-gradient(135deg, #78350f 0%, #451a03 100%)',
        'icons' => '<i class="fa-solid fa-house-chimney-window" style="font-size: 16rem; color: rgba(255,255,255,0.04); position: fixed; left: -2%; top: 25%; transform: rotate(-10deg); z-index: -1;"></i><i class="fa-solid fa-seedling" style="font-size: 10rem; color: rgba(255,255,255,0.03); position: fixed; left: 10%; top: 70%; transform: rotate(15deg); z-index: -1;"></i><i class="fa-solid fa-cow" style="font-size: 18rem; color: rgba(255,255,255,0.04); position: fixed; right: -5%; top: 35%; transform: rotate(10deg); z-index: -1;"></i><i class="fa-solid fa-wheat-awn" style="font-size: 9rem; color: rgba(255,255,255,0.03); position: fixed; right: 8%; top: 15%; transform: rotate(-20deg); z-index: -1;"></i>'
    ],
    'batutumonga' => [
        'bg' => 'linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%)',
        'icons' => '<i class="fa-solid fa-cloud" style="font-size: 20rem; color: rgba(255,255,255,0.04); position: fixed; left: -10%; top: 15%; z-index: -1; filter: blur(4px);"></i><i class="fa-solid fa-wind" style="font-size: 12rem; color: rgba(255,255,255,0.03); position: fixed; left: 5%; top: 60%; transform: rotate(15deg); z-index: -1;"></i><i class="fa-solid fa-cloud-sun" style="font-size: 18rem; color: rgba(255,255,255,0.04); position: fixed; right: -5%; top: 30%; z-index: -1; filter: blur(2px);"></i><i class="fa-solid fa-compass" style="font-size: 10rem; color: rgba(255,255,255,0.02); position: fixed; right: 8%; top: 75%; transform: rotate(-15deg); z-index: -1;"></i>'
    ],
    'default' => [
        'bg' => 'linear-gradient(135deg, #3a2211 0%, #191008 100%)', // Nuansa kayu ukiran Toraja (senada beranda)
        'icons' => '<i class="fa-solid fa-mountain-sun" style="font-size: 16rem; color: rgba(255,255,255,0.05); position: fixed; left: -3%; top: 20%; transform: rotate(-12deg); z-index: -1;"></i><i class="fa-solid fa-leaf" style="font-size: 8rem; color: rgba(255,255,255,0.03); position: fixed; left: 8%; top: 65%; transform: rotate(20deg); z-index: -1;"></i><i class="fa-solid fa-gopuram" style="font-size: 18rem; color: rgba(255,255,255,0.05); position: fixed; right: -5%; top: 35%; transform: rotate(8deg); z-index: -1;"></i><i class="fa-solid fa-hands-holding-circle" style="font-size: 10rem; color: rgba(255,255,255,0.03); position: fixed; right: 6%; top: 15%; transform: rotate(-15deg); z-index: -1;"></i>'
    ]
];
$active_theme = isset($themes[$id]) ? $themes[$id] : (
                isset($themes[str_replace('-', '_', $id)]) ? $themes[str_replace('-', '_', $id)] : $themes['default']
            );
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $selected_materi ? $selected_materi['title'] : 'Materi Edukasi Toraja' ?> | EduToraja</title>
    <!-- Google Fonts: Fraunces (display) + Plus Jakarta Sans (body) -->
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;0,9..144,700;1,9..144,500&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Leaflet.js CSS & JS for Interactive Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <style>
        .custom-div-icon { background: none !important; border: none !important; }

        /* ===== PALET UKIRAN PA'SSURA (senada index.php) ===== */
        :root {
            --ink: #191008;
            --ink-soft: #2a1c10;
            --red: #a5341f;
            --red-deep: #7e2415;
            --gold: #d9a13c;
            --gold-soft: #e8c37c;
            --cream: #f6efe2;
            --cream-deep: #ede1cc;
            --wood: #934b19;
            --text-main: #33241a;
            --text-light: #7d6c5c;
            --shadow: 0 18px 40px -18px rgba(25, 16, 8, 0.45);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--text-main);
        }
        h1, h2, h3, h4 { font-family: 'Fraunces', serif; }
        ::selection { background: var(--gold); color: var(--ink); }

        /* ===== MOTIF ZIGZAG PA'SSURA ===== */
        .passura-strip {
            height: 10px;
            width: 100%;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='10' viewBox='0 0 20 10'%3E%3Crect width='20' height='10' fill='%23191008'/%3E%3Cpath d='M0 10 L5 2 L10 10 Z' fill='%23d9a13c'/%3E%3Cpath d='M10 10 L15 2 L20 10 Z' fill='%23a5341f'/%3E%3C/svg%3E");
            background-repeat: repeat-x;
            background-size: 20px 10px;
        }

        /* ===== PROGRESS BAR MEMBACA ===== */
        .scroll-progress {
            position: fixed;
            top: 0;
            left: 0;
            height: 4px;
            width: 0%;
            background: linear-gradient(90deg, var(--red), var(--gold));
            z-index: 2000;
            transition: width 0.1s linear;
        }

        /* ===== NAVBAR (senada beranda) ===== */
        nav {
            position: sticky;
            top: 0;
            background: rgba(25, 16, 8, 0.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(217, 161, 60, 0.25);
            padding: 16px 50px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
        }

        .logo {
            font-family: 'Fraunces', serif;
            font-size: 1.45rem;
            font-weight: 700;
            color: var(--cream);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .logo i { color: var(--gold); }
        .logo em { font-style: italic; color: var(--gold-soft); }

        .nav-links { list-style: none; display: flex; gap: 30px; }
        .nav-links li a {
            text-decoration: none;
            color: var(--cream);
            font-weight: 600;
            font-size: 0.92rem;
            padding-bottom: 4px;
            border-bottom: 2px solid transparent;
            transition: color 0.3s, border-color 0.3s;
        }
        .nav-links li a:hover { color: var(--gold); border-bottom-color: var(--gold); }

        /* ===== KONTEN ===== */
        .container { max-width: 900px; margin: 50px auto 70px; padding: 0 20px; }

        .back-btn {
            color: var(--gold-soft);
            background: rgba(25, 16, 8, 0.6);
            border: 1px solid rgba(217, 161, 60, 0.5);
            padding: 10px 22px;
            border-radius: 3px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            letter-spacing: 0.04em;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 22px;
            transition: transform 0.3s, background 0.3s, color 0.3s;
        }
        .back-btn:hover {
            transform: translateY(-2px);
            background: var(--red-deep);
            color: var(--cream);
        }

        .article-card {
            background: var(--cream);
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.35);
            border: 1px solid rgba(217, 161, 60, 0.3);
        }

        .article-img-wrap { position: relative; overflow: hidden; }
        .article-img {
            width: 100%;
            height: 400px;
            object-fit: cover;
            display: block;
            transition: transform 8s ease;
        }
        .article-img-wrap:hover .article-img { transform: scale(1.05); }

        .article-body { padding: 44px; }

        /* Bar meta: waktu baca + tombol dengarkan */
        .article-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 14px;
            margin-bottom: 22px;
            padding-bottom: 18px;
            border-bottom: 1px solid rgba(147, 75, 25, 0.2);
        }
        .article-meta .meta-info {
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--wood);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .listen-btn {
            background: var(--ink);
            color: var(--gold-soft);
            border: 1px solid var(--gold);
            padding: 9px 20px;
            border-radius: 24px;
            font-family: inherit;
            font-weight: 700;
            font-size: 0.82rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background 0.3s, color 0.3s, transform 0.2s;
        }
        .listen-btn:hover { background: var(--red-deep); color: var(--cream); transform: translateY(-2px); }

        .article-title {
            font-size: clamp(2rem, 5vw, 2.8rem);
            font-weight: 600;
            line-height: 1.15;
            margin-bottom: 26px;
            color: var(--ink);
        }

        .article-content { line-height: 1.85; font-size: 1.05rem; color: #55483c; }
        .article-content h3 {
            color: var(--ink);
            margin: 34px 0 14px 0;
            font-size: 1.5rem;
            font-weight: 600;
            padding-left: 14px;
            border-left: 4px solid var(--gold);
        }
        .article-content ul { margin-left: 20px; margin-bottom: 20px; }
        .article-content li { margin-bottom: 10px; }
        .article-content li::marker { color: var(--red); }

        /* CKEditor Image Styles */
        .article-content .image { display: table; clear: both; text-align: center; margin: 1.5em auto; }
        .article-content .image img { max-width: 100%; height: auto; border-radius: 6px; }
        .article-content .image.image-style-align-left  { clear: none; float: left;  margin-right: 2em; }
        .article-content .image.image-style-align-right { clear: none; float: right; margin-left: 2em; }
        .article-content .image.image-style-align-center { margin-left: auto; margin-right: auto; }

        /* ===== PETA ===== */
        .map-title {
            color: var(--ink);
            margin-bottom: 15px;
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .map-title i { color: var(--red); }

        #map {
            width: 100%;
            height: 350px;
            border-radius: 8px;
            margin-bottom: 20px;
            border: 1px solid rgba(147, 75, 25, 0.3);
            box-shadow: 0 8px 20px -8px rgba(25, 16, 8, 0.3);
            z-index: 10;
        }

        .map-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: var(--cream-deep);
            border: 1px solid rgba(147, 75, 25, 0.2);
            padding: 16px 20px;
            border-radius: 8px;
            font-size: 0.95rem;
            flex-wrap: wrap;
            gap: 15px;
        }
        .map-info .addr-label {
            font-weight: 700;
            color: var(--ink);
            display: block;
            margin-bottom: 4px;
        }
        .map-info .addr-label i { color: var(--red); }
        .map-info .addr-text { color: var(--text-light); }

        .route-btn {
            background: var(--red);
            color: var(--cream);
            padding: 11px 24px;
            border-radius: 3px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.85rem;
            letter-spacing: 0.04em;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }
        .route-btn:hover {
            background: var(--red-deep);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -8px rgba(165, 52, 31, 0.6);
        }

        /* ===== TIP EDUKASI ===== */
        .edu-tip {
            background: rgba(217, 161, 60, 0.12);
            border: 1px dashed var(--gold);
            padding: 22px 24px;
            border-radius: 8px;
            margin-top: 30px;
            color: var(--cream);
        }
        .edu-tip h4 { color: var(--gold); margin-bottom: 6px; font-size: 1.15rem; }
        .edu-tip p { font-size: 0.95rem; color: #e5d9c3; line-height: 1.7; }
        .edu-tip strong { color: var(--gold-soft); }

        /* ===== BANNER MATERI BELUM DIPILIH ===== */
        .no-materi { text-align: center; padding: 110px 0; color: var(--cream); }
        .no-materi h2 { font-size: 2.2rem; margin-bottom: 12px; color: var(--gold-soft); }
        .no-materi p { color: #d8cbb4; margin-bottom: 26px; font-weight: 300; }
        .no-materi a {
            background: var(--red);
            color: var(--cream);
            padding: 13px 30px;
            border-radius: 3px;
            text-decoration: none;
            font-weight: 700;
            transition: background 0.3s, transform 0.3s;
        }
        .no-materi a:hover { background: var(--red-deep); transform: translateY(-2px); }

        /* ===== POPUP TTS ===== */
        .tts-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(25, 16, 8, 0.7);
            backdrop-filter: blur(4px);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }
        .tts-box {
            background: var(--cream);
            padding: 34px 32px 30px;
            border-radius: 10px;
            text-align: center;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.45);
            border-top: 8px solid transparent;
            border-image: linear-gradient(90deg, var(--red), var(--gold)) 1;
            animation: popIn 0.35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        @keyframes popIn { from { opacity: 0; transform: scale(0.85); } to { opacity: 1; transform: scale(1); } }
        .tts-box i.head-icon { font-size: 2.8rem; color: var(--red); margin-bottom: 14px; }
        .tts-box h3 { margin-bottom: 8px; color: var(--ink); font-size: 1.35rem; }
        .tts-box p { margin-bottom: 24px; color: var(--text-light); line-height: 1.6; font-size: 0.95rem; }
        .tts-actions { display: flex; justify-content: center; gap: 12px; }
        .tts-yes, .tts-no {
            padding: 11px 24px;
            border: none;
            border-radius: 3px;
            font-family: inherit;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            transition: transform 0.2s, background 0.2s;
        }
        .tts-yes { background: var(--red); color: var(--cream); }
        .tts-yes:hover { background: var(--red-deep); transform: translateY(-2px); }
        .tts-no { background: var(--cream-deep); color: var(--text-main); border: 1px solid rgba(147, 75, 25, 0.25); }
        .tts-no:hover { background: #e2d3b8; }

        /* Tombol stop TTS mengambang */
        #tts-stop-btn {
            display: none;
            position: fixed;
            bottom: 30px;
            left: 30px;
            background: var(--red);
            color: var(--cream);
            border: 2px solid var(--gold);
            border-radius: 50%;
            width: 60px;
            height: 60px;
            font-size: 22px;
            box-shadow: 0 10px 24px -6px rgba(165, 52, 31, 0.7);
            cursor: pointer;
            z-index: 9998;
            align-items: center;
            justify-content: center;
            animation: pulse 1.6s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { box-shadow: 0 10px 24px -6px rgba(165, 52, 31, 0.7); }
            50%      { box-shadow: 0 10px 30px 2px rgba(217, 161, 60, 0.55); }
        }

        /* ===== REVEAL SAAT SCROLL ===== */
        .reveal { opacity: 0; transform: translateY(30px); transition: opacity 0.7s ease, transform 0.7s ease; }
        .reveal.visible { opacity: 1; transform: translateY(0); }

        /* ===== GOOGLE TRANSLATE ===== */
        .goog-te-banner-frame, .skiptranslate > iframe.goog-te-banner-frame, #goog-gt-tt { display: none !important; visibility: hidden !important; }
        body { top: 0px !important; }
        .goog-logo-link { display: none !important; }
        .goog-te-gadget { color: transparent !important; font-size: 0px !important; }
        .goog-te-gadget .goog-te-combo {
            padding: 8px 15px;
            border-radius: 3px;
            border: 1px solid rgba(217, 161, 60, 0.5);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 0.85rem;
            color: var(--cream);
            background-color: rgba(255, 253, 248, 0.08);
            outline: none;
            cursor: pointer;
            transition: border-color 0.3s;
            margin: 0;
        }
        .goog-te-gadget .goog-te-combo:hover { border-color: var(--gold); }
        .goog-te-gadget .goog-te-combo option { color: var(--text-main); }

        /* ===== TEMA DINAMIS PER-SLUG (dari database) ===== */
        body {
            background: <?= $active_theme['bg'] ?> !important;
            background-attachment: fixed !important;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 900px) {
            nav { padding: 14px 20px; flex-wrap: wrap; gap: 12px; }
            .nav-links { gap: 18px; }
            #google_translate_element { display: none; }
            .article-body { padding: 28px 22px; }
            .article-img { height: 260px; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; }
            html { scroll-behavior: auto; }
        }
    </style>
</head>
<body>

    <div class="scroll-progress" id="scrollProgress"></div>

    <!-- Dynamic Decorative Icons -->
    <?= $active_theme['icons'] ?>

    <nav>
        <a href="index.php" class="logo"><i class="fa-solid fa-mountain-sun"></i> Edu<em>Toraja</em></a>
        <div style="display: flex; align-items: center; gap: 24px;">
            <!-- Google Translate Widget -->
            <div id="google_translate_element"></div>

            <ul class="nav-links">
                <li><a href="index.php#home">Beranda</a></li>
                <li><a href="index.php#destinasi">Materi Edukasi</a></li>
                <li><a href="admin/">Admin</a></li>
            </ul>
        </div>
    </nav>

    <div class="passura-strip"></div>

    <!-- Google Translate Script -->
    <script type="text/javascript">
    function googleTranslateElementInit() {
      new google.translate.TranslateElement({
          pageLanguage: 'id'
      }, 'google_translate_element');
    }
    </script>
    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>

    <div class="container">
        <?php if($selected_materi): ?>
            <a href="index.php" class="back-btn"><i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda</a>
            <div class="article-card reveal">
                <div class="passura-strip"></div>
                <div class="article-img-wrap">
                    <img src="<?= $selected_materi['image'] ?>" class="article-img" alt="<?= $selected_materi['title'] ?>">
                </div>
                <div class="article-body">
                    <div class="article-meta">
                        <span class="meta-info"><i class="fa-solid fa-book-open"></i> Materi Edukasi &bull; <span id="readTime">&plusmn; 1 menit baca</span></span>
                        <button class="listen-btn" onclick="startTTS()"><i class="fa-solid fa-volume-high"></i> Dengarkan Materi</button>
                    </div>
                    <h1 class="article-title"><?= $selected_materi['title'] ?></h1>
                    <div class="article-content">
                        <?= $selected_materi['content'] ?>
                    </div>

                    <?php if ($has_map): ?>
                        <hr style="margin: 40px 0 30px 0; border: 0; border-top: 1px solid rgba(147, 75, 25, 0.25);">
                        <h3 class="map-title">
                            <i class="fa-solid fa-map-location-dot"></i> Peta Lokasi Wisata
                        </h3>
                        <div id="map"></div>
                        <div class="map-info">
                            <div>
                                <span class="addr-label"><i class="fa-solid fa-location-dot"></i> Alamat Resmi</span>
                                <span class="addr-text"><?= htmlspecialchars($selected_coords['address']) ?></span>
                            </div>
                            <a href="https://www.google.com/maps/dir/?api=1&destination=<?= $selected_coords['lat'] ?>,<?= $selected_coords['lng'] ?>" target="_blank" class="route-btn">
                                <i class="fa-solid fa-diamond-turn-right"></i> Petunjuk Rute Navigasi
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Tip EDUKASI -->
            <div class="edu-tip reveal">
                <h4><i class="fa-solid fa-lightbulb"></i> Fitur Edukasi</h4>
                <p>Ada bagian pada materi ini yang tidak Anda pahami? Anda bisa kembali ke <strong>Beranda</strong> dan membukanya di tombol Widget Chatbot (Kanan Bawah) lalu tanyakan langsung pertanyaannya! Sistem NLP (TF-IDF) kami akan mencari esensi jawaban dari Database.</p>
            </div>

        <?php else: ?>
            <div class="no-materi">
                <h2>Materi Belum Dipilih</h2>
                <p>Silakan akses materi pembelajaran melalui Halaman Beranda.</p>
                <a href="index.php"><i class="fa-solid fa-house"></i> Ke Beranda</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Popup TTS -->
    <div id="tts-popup" class="tts-overlay">
        <div class="tts-box">
            <i class="fa-solid fa-volume-high head-icon"></i>
            <h3>Asisten Suara EduToraja</h3>
            <p>Anda ingin saya membacakan materi ini untuk Anda?</p>
            <div class="tts-actions">
                <button onclick="startTTS()" class="tts-yes"><i class="fa-solid fa-play"></i> Ya, Bacakan</button>
                <button onclick="closeTTSPopup()" class="tts-no">Tidak, Terima Kasih</button>
            </div>
        </div>
    </div>

    <!-- Floating Stop Button (Hidden by default) -->
    <button id="tts-stop-btn" onclick="stopTTS()" aria-label="Hentikan suara">
        <i class="fa-solid fa-stop"></i>
    </button>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tampilkan popup hanya jika ada materi yang sedang dibuka
            <?php if($selected_materi): ?>
            document.getElementById('tts-popup').style.display = 'flex';
            <?php endif; ?>
        });

        let synth = window.speechSynthesis;
        let utterance = null;
        let voices = [];

        // Memuat daftar suara yang tersedia di browser
        function loadVoices() {
            voices = synth.getVoices();
        }

        // Jalankan saat load (beberapa browser memuat suara secara asinkron)
        loadVoices();
        if (speechSynthesis.onvoiceschanged !== undefined) {
            speechSynthesis.onvoiceschanged = loadVoices;
        }

        function closeTTSPopup() {
            document.getElementById('tts-popup').style.display = 'none';
        }

        function startTTS() {
            closeTTSPopup();

            if ('speechSynthesis' in window) {
                // Hentikan dulu jika sedang membaca (agar tombol "Dengarkan" bisa restart)
                synth.cancel();

                const title = document.querySelector('.article-title').innerText;
                const content = document.querySelector('.article-content').innerText;
                const textToRead = title + ". " + content;

                utterance = new SpeechSynthesisUtterance(textToRead);

                // Deteksi bahasa dari Google Translate (Membaca Cookie googtrans)
                let targetLang = 'id-ID'; // Default Bahasa Indonesia
                const gtCookie = document.cookie.match(/googtrans=\/id\/([a-zA-Z-]+)/);
                if (gtCookie) {
                    let langCode = gtCookie[1]; // Contoh: 'en', 'ja', 'fr'
                    if (langCode === 'en') targetLang = 'en-US';
                    else if (langCode === 'ja') targetLang = 'ja-JP';
                    else if (langCode === 'fr') targetLang = 'fr-FR';
                    else targetLang = langCode + '-' + langCode.toUpperCase();
                }

                utterance.lang = targetLang;
                utterance.rate = 0.9;

                // Cari voice yang sesuai dengan target bahasa
                let matchedVoice = null;
                if (targetLang === 'id-ID') {
                    matchedVoice = voices.find(voice => voice.lang === 'id-ID' || voice.lang === 'id_ID');
                    if (!matchedVoice) matchedVoice = voices.find(voice => voice.name.toLowerCase().includes('indonesia'));
                } else {
                    let langPrefix = targetLang.split('-')[0];
                    matchedVoice = voices.find(voice => voice.lang.startsWith(langPrefix));
                }

                if (matchedVoice) {
                    utterance.voice = matchedVoice;
                }

                document.getElementById('tts-stop-btn').style.display = 'flex';

                utterance.onend = function() {
                    document.getElementById('tts-stop-btn').style.display = 'none';
                };

                synth.speak(utterance);
            } else {
                alert("Maaf, browser Anda tidak mendukung fitur Suara (Text-to-Speech).");
            }
        }

        function stopTTS() {
            if (synth) {
                synth.cancel();
                document.getElementById('tts-stop-btn').style.display = 'none';
            }
        }

        // Hentikan suara jika user pindah halaman / menutup tab
        window.addEventListener('beforeunload', function() {
            if (synth) {
                synth.cancel();
            }
        });

        /* ============================================================
           INTERAKSI HALAMAN
           ============================================================ */
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        // 1) Progress bar membaca
        const progressBar = document.getElementById('scrollProgress');
        window.addEventListener('scroll', function() {
            const scrollTop = window.scrollY;
            const docHeight = document.documentElement.scrollHeight - window.innerHeight;
            progressBar.style.width = (docHeight > 0 ? (scrollTop / docHeight) * 100 : 0) + '%';
        }, { passive: true });

        // 2) Estimasi waktu baca (rata-rata 200 kata/menit)
        const contentEl = document.querySelector('.article-content');
        const readTimeEl = document.getElementById('readTime');
        if (contentEl && readTimeEl) {
            const wordCount = contentEl.innerText.trim().split(/\s+/).length;
            const minutes = Math.max(1, Math.round(wordCount / 200));
            readTimeEl.textContent = '\u00B1 ' + minutes + ' menit baca';
        }

        // 3) Elemen muncul bertahap saat di-scroll
        if (!reduceMotion && 'IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.08 });
            document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
        } else {
            document.querySelectorAll('.reveal').forEach(el => el.classList.add('visible'));
        }
    </script>

    <?php if ($has_map): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi Peta Leaflet
            var map = L.map('map', {
                scrollWheelZoom: false // Mencegah zoom otomatis saat scroll halaman
            }).setView([<?= $selected_coords['lat'] ?>, <?= $selected_coords['lng'] ?>], 14);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> kontributor'
            }).addTo(map);

            // Icon kustom warna emas ukiran, senada tema web
            var customIcon = L.divIcon({
                html: '<i class="fa-solid fa-location-dot fa-3x" style="color: #a5341f; text-shadow: 0 2px 5px rgba(0,0,0,0.35);"></i>',
                iconSize: [30, 42],
                iconAnchor: [15, 42],
                popupAnchor: [0, -40],
                className: 'custom-div-icon'
            });

            var marker = L.marker([<?= $selected_coords['lat'] ?>, <?= $selected_coords['lng'] ?>], { icon: customIcon }).addTo(map);
            marker.bindPopup("<div style='font-family: \"Plus Jakarta Sans\", sans-serif; font-size: 0.95rem;'><b><?= htmlspecialchars($selected_materi['title']) ?></b><br><span style='color: #7d6c5c;'><?= htmlspecialchars($selected_coords['address']) ?></span></div>").openPopup();
        });
    </script>
    <?php endif; ?>
</body>
</html>