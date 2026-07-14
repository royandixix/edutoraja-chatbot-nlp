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
        'bg' => 'linear-gradient(135deg, #934b19 0%, #3c1e0a 100%)', // Default Toraja Wood Carving vibe
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
    <!-- Google Fonts & FontAwesome -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Leaflet.js CSS & JS for Interactive Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    
    <style>
        .custom-div-icon {
            background: none !important;
            border: none !important;
        }
        /* Shared Styles based on index.php */
        :root {
            --primary: #934b19; /* Toraja wood carving brown */
            --primary-dark: #73350f;
            --bg-color: #faf6f0; /* Premium soft light brown-cream background */
            --text-main: #3c2f2f; /* Deep chocolate brown */
            --text-light: #7c6e6e; /* Soft brown-gray */
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Outfit', sans-serif; }
        body { background-color: var(--bg-color); color: var(--text-main); }
        
        nav { background: white; padding: 20px 50px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .logo { font-size: 1.5rem; font-weight: 800; color: var(--text-main); text-decoration: none; }
        .logo i { color: var(--primary); }
        .nav-links { list-style: none; display: flex; gap: 30px; }
        .nav-links li a { text-decoration: none; color: var(--text-main); font-weight: 600; }

        .container { max-width: 900px; margin: 50px auto; padding: 0 20px; }
        .back-btn { color: var(--primary); text-decoration: none; font-weight: 600; display: inline-block; margin-bottom: 20px; }
        
        .article-card { background: white; border-radius: 20px; overflow: hidden; box-shadow: 0 10px 30px -5px rgba(0,0,0,0.05); }
        .article-img { width: 100%; height: 400px; object-fit: cover; }
        .article-body { padding: 40px; }
        .article-title { font-size: 2.5rem; font-weight: 800; margin-bottom: 25px; color: var(--text-main); }
        .article-content { line-height: 1.8; font-size: 1.1rem; color: var(--text-light); }
        .article-content h3 { color: var(--text-main); margin: 30px 0 15px 0; font-size: 1.5rem; }
        .article-content ul { margin-left: 20px; margin-bottom: 20px; }
        .article-content li { margin-bottom: 10px; }
        
        /* CKEditor Image Styles */
        .article-content .image {
            display: table;
            clear: both;
            text-align: center;
            margin: 1.5em auto;
        }
        .article-content .image img {
            max-width: 100%;
            height: auto;
        }
        .article-content .image.image-style-align-left {
            clear: none;
            float: left;
            margin-right: 2em;
        }
        .article-content .image.image-style-align-right {
            clear: none;
            float: right;
            margin-left: 2em;
        }
        .article-content .image.image-style-align-center {
            margin-left: auto;
            margin-right: auto;
        }

        /* Banner jika belum pilih materi */
        .no-materi { text-align: center; padding: 100px 0; }
        .no-materi h2 { font-size: 2rem; margin-bottom: 10px; }
        
        /* CUSTOM GOOGLE TRANSLATE STYLING */
        .goog-te-banner-frame, .skiptranslate > iframe.goog-te-banner-frame, #goog-gt-tt { display: none !important; visibility: hidden !important; } 
        body { top: 0px !important; }
        .goog-logo-link { display:none !important; } 
        .goog-te-gadget { color: transparent !important; font-size: 0px !important; }
        .goog-te-gadget .goog-te-combo {
            padding: 8px 15px;
            border-radius: 20px;
            border: 1px solid #cbd5e1;
            font-family: 'Outfit', sans-serif;
            font-size: 0.9rem;
            color: var(--text-main);
            background-color: white;
            outline: none;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            transition: border-color 0.3s;
            margin: 0;
        }
        .goog-te-gadget .goog-te-combo:hover {
            border-color: var(--primary);
        }

        /* DYNAMIC THEME OVERRIDES */
        body {
            background: <?= $active_theme['bg'] ?> !important;
            background-attachment: fixed !important;
        }
        
        .article-card {
            background: rgba(255, 255, 255, 0.97) !important;
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255,255,255,0.2);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2) !important;
        }
        
        .back-btn {
            background: rgba(255, 255, 255, 0.9);
            padding: 10px 20px;
            border-radius: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s, background 0.3s;
        }
        .back-btn:hover {
            transform: translateY(-2px);
            background: white;
        }
    </style>
</head>
<body>
    
    <!-- Dynamic Decorative Icons -->
    <?= $active_theme['icons'] ?>

    <nav>
        <a href="index.php" class="logo"><i class="fa-solid fa-mountain-sun"></i> EduToraja</a>
        <div style="display: flex; align-items: center; gap: 20px;">
            <!-- Google Translate Widget -->
            <div id="google_translate_element"></div>
            
            <ul class="nav-links">
                <li><a href="index.php#home">Beranda</a></li>
                <li><a href="index.php#destinasi">Materi Edukasi</a></li>
                <li><a href="admin/">Admin</a></li>
            </ul>
        </div>
    </nav>

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
            <div class="article-card">
                <img src="<?= $selected_materi['image'] ?>" class="article-img" alt="<?= $selected_materi['title'] ?>">
                <div class="article-body">
                    <h1 class="article-title"><?= $selected_materi['title'] ?></h1>
                    <div class="article-content">
                        <?= $selected_materi['content'] ?>
                    </div>

                    <?php if ($has_map): ?>
                        <hr style="margin: 40px 0 30px 0; border: 0; border-top: 1px solid #e2e8f0;">
                        <h3 style="color: var(--text-main); margin-bottom: 15px; font-size: 1.5rem; display: flex; align-items: center; gap: 10px;">
                            <i class="fa-solid fa-map-location-dot" style="color: var(--primary);"></i> Peta Lokasi Wisata
                        </h3>
                        <div id="map" style="width: 100%; height: 350px; border-radius: 15px; margin-bottom: 20px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); z-index: 10;"></div>
                        <div style="display: flex; justify-content: space-between; align-items: center; background: #f1f5f9; padding: 15px 20px; border-radius: 12px; font-size: 0.95rem; flex-wrap: wrap; gap: 15px;">
                            <div>
                                <span style="font-weight: 700; color: var(--text-main); display: block; margin-bottom: 4px;"><i class="fa-solid fa-location-dot" style="color: var(--primary);"></i> Alamat Resmi</span>
                                <span style="color: var(--text-light);"><?= htmlspecialchars($selected_coords['address']) ?></span>
                            </div>
                            <a href="https://www.google.com/maps/dir/?api=1&destination=<?= $selected_coords['lat'] ?>,<?= $selected_coords['lng'] ?>" target="_blank" style="background: var(--primary); color: white; padding: 10px 22px; border-radius: 30px; text-decoration: none; font-weight: 700; font-size: 0.85rem; display: flex; align-items: center; gap: 8px; transition: all 0.3s; box-shadow: 0 4px 6px rgba(245, 158, 11, 0.2);" onmouseover="this.style.background='var(--primary-dark)'; this.style.transform='translateY(-2px)'" onmouseout="this.style.background='var(--primary)'; this.style.transform='translateY(0)'">
                                <i class="fa-solid fa-diamond-turn-right"></i> Petunjuk Rute Navigasi
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Tip EDUKASI -->
            <div style="background: rgba(245, 158, 11, 0.1); border: 1px dashed var(--primary); padding: 20px; border-radius: 15px; margin-top: 30px;">
                <h4 style="color: var(--primary-dark); margin-bottom: 5px;"><i class="fa-solid fa-lightbulb"></i> Fitur Edukasi</h4>
                <p style="font-size: 0.95rem; color: var(--text-light);">Ada bagian pada materi ini yang tidak Anda pahami? Anda bisa kembali ke <strong>Beranda</strong> dan membukanya di tombol Widget Chatbot (Kanan Bawah) lalu tanyakan langsung pertanyaannya! Sistem NLP (TF-IDF) kami akan mencari esensi jawaban dari Database.</p>
            </div>

        <?php else: ?>
            <div class="no-materi">
                <h2>Materi Belum Dipilih</h2>
                <p style="color: var(--text-light); margin-bottom: 20px;">Silakan akses materi pembelajaran melalui Halaman Beranda.</p>
                <a href="index.php" style="background: var(--primary); color: white; padding: 10px 20px; border-radius: 20px; text-decoration: none;">Ke Beranda</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Popup TTS -->
    <div id="tts-popup" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;">
        <div style="background:white; padding:30px; border-radius:15px; text-align:center; max-width:400px; width:90%; box-shadow:0 10px 25px rgba(0,0,0,0.2);">
            <i class="fa-solid fa-volume-high" style="font-size:3rem; color:var(--primary); margin-bottom:15px;"></i>
            <h3 style="margin-bottom:10px; color:var(--text-main);">Asisten Suara EduToraja</h3>
            <p style="margin-bottom:25px; color:var(--text-light); line-height:1.5;">Anda ingin saya membacakan materi ini untuk Anda?</p>
            <div style="display:flex; justify-content:center; gap:15px;">
                <button onclick="startTTS()" style="padding:10px 25px; background:var(--primary); color:white; border:none; border-radius:8px; font-weight:bold; cursor:pointer; transition:transform 0.2s;">Ya, Bacakan</button>
                <button onclick="closeTTSPopup()" style="padding:10px 25px; background:#e2e8f0; color:var(--text-main); border:none; border-radius:8px; font-weight:bold; cursor:pointer; transition:background 0.2s;">Tidak, Terima Kasih</button>
            </div>
        </div>
    </div>

    <!-- Floating Stop Button (Hidden by default) -->
    <button id="tts-stop-btn" onclick="stopTTS()" style="display:none; position:fixed; bottom:30px; left:30px; background:#ef4444; color:white; border:none; border-radius:50%; width:60px; height:60px; font-size:24px; box-shadow:0 5px 15px rgba(239, 68, 68, 0.4); cursor:pointer; z-index:9998; align-items:center; justify-content:center;">
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

            // Icon kustom berwarna amber/orange senada dengan tema web
            var customIcon = L.divIcon({
                html: '<i class="fa-solid fa-location-dot fa-3x" style="color: #d97706; text-shadow: 0 2px 5px rgba(0,0,0,0.3);"></i>',
                iconSize: [30, 42],
                iconAnchor: [15, 42],
                popupAnchor: [0, -40],
                className: 'custom-div-icon'
            });

            var marker = L.marker([<?= $selected_coords['lat'] ?>, <?= $selected_coords['lng'] ?>], { icon: customIcon }).addTo(map);
            marker.bindPopup("<div style='font-family: \"Outfit\", sans-serif; font-size: 0.95rem;'><b><?= htmlspecialchars($selected_materi['title']) ?></b><br><span style='color: #64748b;'><?= htmlspecialchars($selected_coords['address']) ?></span></div>").openPopup();
        });
    </script>
    <?php endif; ?>
</body>
</html>
