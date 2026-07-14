<?php require_once 'config.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toraja Eduwisata - Jelajahi Budaya &amp; Pariwisata</title>
    <!-- Google Fonts: Fraunces (display) + Plus Jakarta Sans (body, karya desainer Indonesia) -->
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;0,9..144,700;1,9..144,500&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* ================= PALET UKIRAN PA'SSURA =================
           malotong (hitam) - mararang (merah) - mariri (emas) - mabusa (krem) */
        :root {
            --ink: #191008;          /* malotong: hitam kayu */
            --ink-soft: #2a1c10;
            --red: #a5341f;          /* mararang: merah ukiran */
            --red-deep: #7e2415;
            --gold: #d9a13c;         /* mariri: kuning emas */
            --gold-soft: #e8c37c;
            --cream: #f6efe2;        /* mabusa: putih kapur */
            --cream-deep: #ede1cc;
            --wood: #934b19;         /* coklat kayu tongkonan */
            --text-main: #33241a;
            --text-light: #7d6c5c;
            --shadow: 0 18px 40px -18px rgba(25, 16, 8, 0.45);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--cream);
            color: var(--text-main);
            overflow-x: hidden;
        }

        h1, h2, h3, .display {
            font-family: 'Fraunces', serif;
        }

        ::selection { background: var(--gold); color: var(--ink); }

        /* ===== MOTIF ZIGZAG PA'SSURA (pembatas antar bagian) ===== */
        .passura-divider {
            height: 14px;
            width: 100%;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='28' height='14' viewBox='0 0 28 14'%3E%3Cpath d='M0 14 L7 2 L14 14 Z' fill='%23a5341f'/%3E%3Cpath d='M14 14 L21 2 L28 14 Z' fill='%23d9a13c'/%3E%3C/svg%3E");
            background-repeat: repeat-x;
            background-size: 28px 14px;
        }
        .passura-divider.flip { transform: scaleY(-1); }

        /* ===== ORNAMEN GARIS JUDUL ===== */
        .ornament {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            margin-bottom: 18px;
        }
        .ornament::before, .ornament::after {
            content: '';
            width: 46px;
            height: 2px;
            background: var(--gold);
        }
        .ornament i { color: var(--gold); font-size: 0.85rem; }

        .eyebrow {
            display: block;
            text-align: center;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 0.35em;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 12px;
        }

        /* ================= NAVBAR ================= */
        nav {
            position: fixed;
            top: 0;
            width: 100%;
            background: rgba(25, 16, 8, 0.88);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(217, 161, 60, 0.25);
            padding: 16px 50px;
            display: grid;
            grid-template-columns: 1fr auto 1fr;
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
            letter-spacing: 0.02em;
        }
        .logo i { color: var(--gold); }
        .logo em { font-style: italic; color: var(--gold-soft); }

        .nav-links {
            list-style: none;
            display: flex;
            gap: 34px;
        }

        .nav-links li a {
            text-decoration: none;
            color: var(--cream);
            font-weight: 600;
            font-size: 0.92rem;
            letter-spacing: 0.03em;
            padding-bottom: 4px;
            border-bottom: 2px solid transparent;
            transition: color 0.3s, border-color 0.3s;
        }

        .nav-links li a:hover {
            color: var(--gold);
            border-bottom-color: var(--gold);
        }

        /* DROPDOWN MENU */
        .dropdown { position: relative; }

        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 50%;
            transform: translateX(-50%);
            background-color: var(--ink);
            min-width: 240px;
            box-shadow: var(--shadow);
            border-radius: 4px;
            border: 1px solid rgba(217, 161, 60, 0.35);
            border-top: 3px solid var(--gold);
            padding: 8px 0;
            z-index: 1001;
            list-style: none;
            margin-top: 16px;
        }

        /* Jembatan hover agar dropdown tidak menutup */
        .dropdown::after {
            content: '';
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            height: 20px;
        }

        .dropdown:hover .dropdown-menu { display: block; }

        .dropdown-menu li { width: 100%; }

        .dropdown-menu li a {
            color: var(--cream) !important;
            padding: 12px 20px;
            text-decoration: none;
            display: block;
            font-size: 0.88rem;
            font-weight: 500;
            border-bottom: none !important;
            transition: background-color 0.3s, color 0.3s, padding-left 0.3s;
            text-align: left;
            white-space: nowrap;
        }

        .dropdown-menu li a:hover {
            background-color: rgba(217, 161, 60, 0.12);
            color: var(--gold) !important;
            padding-left: 26px;
        }

        /* ================= HERO ================= */
        .hero {
            min-height: 100vh;
            background: linear-gradient(rgba(25, 16, 8, 0.55), rgba(25, 16, 8, 0.82)), url('bg-hero.jpg') center/cover fixed no-repeat;
            background-color: var(--ink-soft);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 120px 20px 80px;
            position: relative;
        }

        /* Bingkai ukiran tipis di dalam hero */
        .hero::before {
            content: '';
            position: absolute;
            inset: 90px 28px 28px;
            border: 1px solid rgba(217, 161, 60, 0.35);
            pointer-events: none;
        }

        .hero-content {
            max-width: 880px;
            color: var(--cream);
            animation: fadeIn 1.1s ease-out;
            position: relative;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .hero .eyebrow { color: var(--gold-soft); }

        .hero h1 {
            font-size: clamp(2.6rem, 6.5vw, 4.6rem);
            font-weight: 600;
            line-height: 1.08;
            margin-bottom: 24px;
            letter-spacing: -0.01em;
        }

        .hero h1 em {
            font-style: italic;
            color: var(--gold);
        }

        .hero p {
            font-size: 1.1rem;
            font-weight: 300;
            margin: 0 auto 38px;
            max-width: 640px;
            color: #e9dfcd;
            line-height: 1.75;
        }

        .hero-actions {
            display: flex;
            gap: 16px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 15px 36px;
            background: var(--red);
            color: var(--cream);
            text-decoration: none;
            font-weight: 700;
            font-size: 0.95rem;
            letter-spacing: 0.05em;
            border-radius: 3px;
            border: 1px solid var(--red);
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background: var(--red-deep);
            transform: translateY(-3px);
            box-shadow: 0 14px 26px -10px rgba(165, 52, 31, 0.6);
        }

        .btn-ghost {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 15px 36px;
            background: transparent;
            color: var(--gold-soft);
            text-decoration: none;
            font-weight: 700;
            font-size: 0.95rem;
            letter-spacing: 0.05em;
            border-radius: 3px;
            border: 1px solid rgba(217, 161, 60, 0.6);
            transition: all 0.3s;
        }

        .btn-ghost:hover {
            background: rgba(217, 161, 60, 0.14);
            color: var(--gold);
            transform: translateY(-3px);
        }

        /* ================= JUDUL SEKSI ================= */
        .section-title {
            text-align: center;
            font-size: clamp(2rem, 4.5vw, 2.9rem);
            font-weight: 600;
            margin-bottom: 16px;
            color: var(--cream);
            letter-spacing: -0.01em;
        }
        .section-sub {
            text-align: center;
            max-width: 620px;
            margin: 0 auto 56px;
            color: #d8cbb4;
            font-weight: 300;
            line-height: 1.7;
        }

        /* ================= SEKILAS TENTANG ================= */
        .about-cols {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 0;
            max-width: 1100px;
            margin: 0 auto;
        }

        .about-col {
            padding: 36px 34px;
            border-left: 1px solid rgba(217, 161, 60, 0.28);
        }
        .about-col:first-child { border-left: none; }

        .about-col .icon-badge {
            width: 52px;
            height: 52px;
            border: 1px solid var(--gold);
            border-radius: 2px;
            transform: rotate(45deg);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 26px;
        }
        .about-col .icon-badge i {
            transform: rotate(-45deg);
            color: var(--gold);
            font-size: 1.2rem;
        }

        .about-col h3 {
            color: var(--gold-soft);
            margin-bottom: 14px;
            font-size: 1.45rem;
            font-weight: 600;
        }

        .about-col p {
            color: #d8cbb4;
            font-weight: 300;
            font-size: 0.98rem;
            line-height: 1.8;
        }
        .about-col p i { color: var(--gold-soft); }

        /* ================= KARTU (DESTINASI & BUDAYA) ================= */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 34px;
        }

        .card {
            background: var(--cream);
            border-radius: 6px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: transform 0.35s ease, box-shadow 0.35s ease;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        /* Balok ukiran di puncak kartu */
        .card::before {
            content: '';
            display: block;
            height: 10px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='10' viewBox='0 0 20 10'%3E%3Crect width='20' height='10' fill='%23191008'/%3E%3Cpath d='M0 10 L5 2 L10 10 Z' fill='%23d9a13c'/%3E%3Cpath d='M10 10 L15 2 L20 10 Z' fill='%23a5341f'/%3E%3C/svg%3E");
            background-repeat: repeat-x;
            background-size: 20px 10px;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 28px 50px -18px rgba(25, 16, 8, 0.6);
        }

        .card .card-img-wrap {
            overflow: hidden;
            height: 210px;
        }

        .card img {
            width: 100%;
            height: 210px;
            object-fit: cover;
            transition: transform 0.6s ease;
            display: block;
        }

        .card:hover img { transform: scale(1.07); }

        .card-content {
            padding: 26px 26px 28px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .card-title {
            color: var(--ink);
            font-size: 1.45rem;
            font-weight: 600;
            margin-bottom: 10px;
            line-height: 1.25;
        }

        .card-desc {
            color: var(--text-light);
            font-size: 0.93rem;
            line-height: 1.7;
            margin-bottom: 22px;
            flex: 1;
        }

        .btn-readmore {
            color: var(--red);
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: gap 0.3s, color 0.3s;
        }

        .btn-readmore:hover { gap: 14px; color: var(--red-deep); }

        .empty-note {
            text-align: center;
            width: 100%;
            color: #d8cbb4;
            font-weight: 300;
            grid-column: 1 / -1;
            padding: 30px;
            border: 1px dashed rgba(217, 161, 60, 0.4);
            border-radius: 6px;
        }

        /* ================= FOOTER ================= */
        footer {
            background: var(--ink);
            color: #cdbfa8;
            text-align: center;
            padding: 54px 20px 44px;
        }

        footer .logo {
            justify-content: center;
            margin-bottom: 14px;
            font-size: 1.3rem;
        }

        footer p {
            font-size: 0.88rem;
            font-weight: 300;
            letter-spacing: 0.02em;
        }

        /* ================= CHATBOT ================= */
        .chatbot-btn {
            position: fixed;
            bottom: 40px;
            right: 40px;
            width: 68px;
            height: 68px;
            background: var(--red);
            color: var(--cream);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            box-shadow: 0 14px 30px -8px rgba(165, 52, 31, 0.7);
            cursor: pointer;
            z-index: 1000;
            transition: transform 0.3s, background 0.3s;
            border: 2px solid var(--gold);
            outline: none;
        }

        .chatbot-btn:hover { transform: scale(1.08); background: var(--red-deep); }

        .chatbot-panel {
            position: fixed;
            bottom: 122px;
            right: 40px;
            width: 380px;
            height: 550px;
            max-width: calc(100vw - 40px);
            background: var(--cream);
            border-radius: 14px;
            box-shadow: 0 24px 60px rgba(25, 16, 8, 0.4);
            z-index: 999;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transform: scale(0);
            transform-origin: bottom right;
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            border: 1px solid rgba(217, 161, 60, 0.4);
        }

        .chatbot-panel.active { transform: scale(1); }

        .chat-header {
            background: linear-gradient(135deg, var(--ink) 0%, var(--red-deep) 130%);
            color: var(--cream);
            padding: 18px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px solid var(--gold);
        }

        .chat-header-info { display: flex; align-items: center; gap: 14px; }
        .chat-header-info > i { color: var(--gold); }

        .chat-header h3 {
            font-size: 1.1rem;
            font-weight: 600;
            font-family: 'Fraunces', serif;
        }

        .chat-header p { font-size: 0.75rem; opacity: 0.85; letter-spacing: 0.04em; }

        .close-chat { cursor: pointer; font-size: 1.15rem; opacity: 0.85; transition: opacity 0.2s; }
        .close-chat:hover { opacity: 1; }

        .chat-body {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
            background: var(--cream-deep);
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .message { max-width: 85%; font-size: 0.9rem; line-height: 1.55; }

        .message.bot { align-self: flex-start; }

        .message.bot .msg-bubble {
            background: #fffdf8;
            padding: 12px 18px;
            border-radius: 12px 12px 12px 2px;
            box-shadow: 0 2px 6px rgba(25, 16, 8, 0.08);
            border: 1px solid rgba(147, 75, 25, 0.18);
            border-left: 3px solid var(--gold);
        }

        .message.user { align-self: flex-end; }

        .message.user .msg-bubble {
            background: var(--red);
            color: var(--cream);
            padding: 12px 18px;
            border-radius: 12px 12px 2px 12px;
        }

        .score-badge {
            font-size: 0.7rem;
            color: var(--text-light);
            margin-top: 6px;
            display: block;
        }

        .chat-footer {
            padding: 14px;
            background: var(--cream);
            border-top: 1px solid rgba(147, 75, 25, 0.2);
            display: flex;
            gap: 10px;
        }

        .chat-input {
            flex: 1;
            padding: 12px 16px;
            border: 1px solid rgba(147, 75, 25, 0.35);
            border-radius: 24px;
            outline: none;
            font-family: inherit;
            background: #fffdf8;
            color: var(--text-main);
        }

        .chat-input:focus { border-color: var(--red); box-shadow: 0 0 0 3px rgba(165, 52, 31, 0.12); }

        .send-btn {
            background: var(--red);
            color: var(--cream);
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            cursor: pointer;
            transition: transform 0.2s, background 0.2s;
        }

        .send-btn:hover { transform: scale(1.06); background: var(--red-deep); }

        .typing-indicator {
            display: none;
            align-self: flex-start;
            background: #fffdf8;
            padding: 12px 18px;
            border-radius: 12px 12px 12px 2px;
            border: 1px solid rgba(147, 75, 25, 0.18);
        }
        .dot { height: 6px; width: 6px; background: var(--wood); border-radius: 50%; display: inline-block; margin: 0 1px; animation: blink 1.4s infinite both; }
        .dot:nth-child(2) { animation-delay: 0.2s; }
        .dot:nth-child(3) { animation-delay: 0.4s; }
        @keyframes blink { 0%, 100% { opacity: 0.2; transform: scale(0.8); } 50% { opacity: 1; transform: scale(1.2); } }

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

        /* ===== RESPONSIVE ===== */
        @media (max-width: 900px) {
            nav { padding: 14px 20px; grid-template-columns: auto 1fr; gap: 12px; }
            .nav-links { gap: 18px; flex-wrap: wrap; justify-content: flex-end; }
            #google_translate_element { display: none; }
            .about-col { border-left: none; border-top: 1px solid rgba(217, 161, 60, 0.28); }
            .about-col:first-child { border-top: none; }
            .chatbot-panel { right: 20px; bottom: 110px; }
            .chatbot-btn { right: 20px; bottom: 28px; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; }
            html { scroll-behavior: auto; }
        }
    </style>
</head>
<body>

    <nav>
        <a href="#" class="logo" style="justify-self: start;"><i class="fa-solid fa-mountain-sun"></i> Edu<em>Toraja</em></a>
        <ul class="nav-links" style="justify-self: center;">
            <li><a href="#home">Beranda</a></li>
            <li class="dropdown">
                <a href="#destinasi" class="dropdown-trigger">Materi Destinasi <i class="fa-solid fa-chevron-down" style="font-size: 0.7rem; margin-left: 4px;"></i></a>
                <ul class="dropdown-menu">
                    <?php
                    $nav_dest = $conn->query("SELECT nama, slug FROM destinasi ORDER BY id ASC");
                    if ($nav_dest && $nav_dest->num_rows > 0) {
                        while($d_row = $nav_dest->fetch_assoc()) {
                            echo "<li><a href='materi.php?id=" . htmlspecialchars($d_row['slug']) . "'>" . htmlspecialchars($d_row['nama']) . "</a></li>";
                        }
                    } else {
                        echo "<li><a href='#'>Belum ada objek wisata</a></li>";
                    }
                    ?>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#budaya" class="dropdown-trigger">Materi Budaya <i class="fa-solid fa-chevron-down" style="font-size: 0.7rem; margin-left: 4px;"></i></a>
                <ul class="dropdown-menu">
                    <?php
                    $nav_bud = $conn->query("SELECT nama, slug FROM budaya ORDER BY id ASC");
                    if ($nav_bud && $nav_bud->num_rows > 0) {
                        while($b_row = $nav_bud->fetch_assoc()) {
                            echo "<li><a href='materi.php?id=" . htmlspecialchars($b_row['slug']) . "'>" . htmlspecialchars($b_row['nama']) . "</a></li>";
                        }
                    } else {
                        echo "<li><a href='#'>Belum ada budaya adat</a></li>";
                    }
                    ?>
                </ul>
            </li>
            <li><a href="admin/">Admin</a></li>
        </ul>
        <!-- Google Translate Widget -->
        <div id="google_translate_element" style="justify-self: end;"></div>
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

    <!-- ================= HERO ================= -->
    <section class="hero" id="home">
        <div class="hero-content">
            <span class="eyebrow">Tana Toraja &bull; Sulawesi Selatan</span>
            <h1>Menjelajah <em>Negeri di Atas Awan</em>,<br>Belajar dari Tanah Leluhur</h1>
            <p>Sistem Edukasi Pariwisata Toraja Interaktif. Pelajari sejarah, objek wisata, dan budaya adat — lalu tanyakan apa saja mengenai Toraja pada Chatbot NLP kami.</p>
            <div class="hero-actions">
                <a href="#tentang" class="btn-primary">Mulai Belajar <i class="fa-solid fa-arrow-right"></i></a>
                <a href="#destinasi" class="btn-ghost"><i class="fa-solid fa-map-location-dot"></i> Lihat Destinasi</a>
            </div>
        </div>
    </section>

    <div class="passura-divider"></div>

    <!-- ================= SEKILAS TENTANG TORAJA ================= -->
    <section id="tentang" style="width: 100%; margin: 0; padding: 100px 20px; background: linear-gradient(rgba(25, 16, 8, 0.88), rgba(25, 16, 8, 0.88)), url('bg-sekilas.jpg') center/cover fixed no-repeat; color: var(--cream);">
        <div style="max-width: 1200px; margin: 0 auto;">
            <span class="eyebrow">Kenali Toraja</span>
            <h2 class="section-title">Sekilas Tentang Toraja</h2>
            <div class="ornament"><i class="fa-solid fa-diamond"></i></div>
            <p class="section-sub">Tiga pintu masuk untuk memahami tanah para leluhur: alamnya, kepercayaannya, dan semangat belajarnya.</p>

            <div class="about-cols">
                <div class="about-col">
                    <div class="icon-badge"><i class="fa-solid fa-map-location-dot"></i></div>
                    <h3>Negeri di Atas Awan</h3>
                    <p>Tana Toraja adalah sebuah permata tersembunyi di pegunungan Provinsi Sulawesi Selatan. Wilayah ini tidak hanya menawarkan pemandangan alam berupa bentang pegunungan kapur dan terasering sawah yang memukau, tetapi juga kekayaan budaya leluhur yang masih dipegang teguh hingga era modern.</p>
                </div>
                <div class="about-col">
                    <div class="icon-badge"><i class="fa-solid fa-masks-theater"></i></div>
                    <h3>Aluk To Dolo</h3>
                    <p>Kepercayaan animisme purba <i>Aluk To Dolo</i> (Jalan Leluhur) menjadi fondasi kehidupan sosial masyarakat Toraja. Mulai dari ukiran kayu (Pa'ssura) yang sarat makna, rumah adat Tongkonan yang ikonis, hingga perayaan siklus kehidupan dan kematian yang menjadi magnet daya tarik bagi wisatawan dari seluruh penjuru dunia.</p>
                </div>
                <div class="about-col">
                    <div class="icon-badge"><i class="fa-solid fa-graduation-cap"></i></div>
                    <h3>Misi Eduwisata</h3>
                    <p>EduToraja hadir untuk menjembatani pesona pariwisata Toraja dengan nilai-nilai edukasi. Kami meyakini bahwa berwisata ke Toraja bukan sekadar melihat keindahan, melainkan mempelajari sejarah peradaban dan filosofi kehidupan yang luar biasa. Tanyakan pada Chatbot kami jika Anda penasaran!</p>
                </div>
            </div>
        </div>
    </section>

    <div class="passura-divider flip"></div>

    <!-- ================= DESTINASI ================= -->
    <section id="destinasi" style="width: 100%; margin: 0; padding: 100px 20px; background: linear-gradient(rgba(25, 16, 8, 0.72), rgba(25, 16, 8, 0.72)), url('bg-destinasi.jpg') center/cover fixed no-repeat; color: var(--cream);">
        <div style="max-width: 1200px; margin: 0 auto;">
            <span class="eyebrow">Jelajahi Alamnya</span>
            <h2 class="section-title">Destinasi Objek Wisata</h2>
            <div class="ornament"><i class="fa-solid fa-diamond"></i></div>
            <p class="section-sub">Pilih destinasi, buka materinya, dan pelajari kisah di balik setiap tempat.</p>
            <div class="grid">
            <?php
            // Fetch dynamic destinations from database
            $dest_result = $conn->query("SELECT * FROM destinasi ORDER BY id ASC");
            if ($dest_result && $dest_result->num_rows > 0) {
                while($row = $dest_result->fetch_assoc()) {
            ?>
            <div class="card">
                <div class="card-img-wrap">
                    <img src="<?= htmlspecialchars($row['gambar_url']) ?>" alt="<?= htmlspecialchars($row['nama']) ?>">
                </div>
                <div class="card-content">
                    <h3 class="card-title"><?= htmlspecialchars($row['nama']) ?></h3>
                    <p class="card-desc"><?= htmlspecialchars($row['deskripsi']) ?></p>
                    <a href="materi.php?id=<?= htmlspecialchars($row['slug']) ?>" class="btn-readmore">Pelajari Materi <i class="fa-solid fa-arrow-right"></i></a>
                </div>
            </div>
            <?php
                }
            } else {
                echo "<div class='empty-note'>Belum ada objek wisata yang ditambahkan.</div>";
            }
            ?>
            </div> <!-- End Grid -->
        </div> <!-- End Max-Width Wrapper -->
    </section>

    <div class="passura-divider"></div>

    <!-- ================= BUDAYA ================= -->
    <section id="budaya" style="width: 100%; margin: 0; padding: 100px 20px; background: linear-gradient(rgba(25, 16, 8, 0.72), rgba(25, 16, 8, 0.72)), url('bg-ukiran.jpg') center/cover fixed no-repeat; color: var(--cream);">
        <div style="max-width: 1200px; margin: 0 auto;">
            <span class="eyebrow">Warisan Leluhur</span>
            <h2 class="section-title">Budaya &amp; Tradisi Adat</h2>
            <div class="ornament"><i class="fa-solid fa-diamond"></i></div>
            <p class="section-sub">Dari ukiran Pa'ssura hingga upacara adat — setiap tradisi menyimpan filosofi kehidupan.</p>
            <div class="grid">
                <?php
                // Fetch dynamic budaya from database
                $bud_result = $conn->query("SELECT * FROM budaya ORDER BY id ASC");
                if ($bud_result && $bud_result->num_rows > 0) {
                    while($row = $bud_result->fetch_assoc()) {
                ?>
                <div class="card">
                    <div class="card-img-wrap">
                        <img src="<?= htmlspecialchars($row['gambar_url']) ?>" alt="<?= htmlspecialchars($row['nama']) ?>">
                    </div>
                    <div class="card-content">
                        <h3 class="card-title"><?= htmlspecialchars($row['nama']) ?></h3>
                        <p class="card-desc"><?= htmlspecialchars($row['deskripsi']) ?></p>
                        <a href="materi.php?id=<?= htmlspecialchars($row['slug']) ?>" class="btn-readmore">Pelajari Materi <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
                <?php
                    }
                } else {
                    echo "<div class='empty-note'>Belum ada budaya adat yang ditambahkan.</div>";
                }
                ?>
            </div>
        </div>
    </section>

    <div class="passura-divider flip"></div>

    <footer>
        <a href="#" class="logo"><i class="fa-solid fa-mountain-sun"></i> Edu<em>Toraja</em></a>
        <p>&copy; <?= date("Y") ?> EduToraja - Sistem Edukasi Pariwisata &amp; Budaya. Dibuat dengan Kecerdasan Bahasa (NLP).</p>
    </footer>

    <!-- Chatbot Floating Button -->
    <button class="chatbot-btn" id="chatbotToggle"><i class="fa-solid fa-comment-dots"></i></button>

    <!-- Chatbot Panel -->
    <div class="chatbot-panel" id="chatbotPanel">
        <div class="chat-header">
            <div class="chat-header-info">
                <i class="fa-solid fa-robot fa-2x"></i>
                <div>
                    <h3>Toraja EduBot</h3>
                    <p>NLP Engine: TF-IDF Aktif</p>
                </div>
            </div>
            <i class="fa-solid fa-times close-chat" id="chatbotClose"></i>
        </div>
        <div class="chat-body" id="chatBox">
            <div class="message bot">
                <div class="msg-bubble">
                    Halo! Jika ada materi sejarah, wisata, atau budaya Toraja yang tidak Anda pahami dari website, tanyakan saja padaku! 😄
                </div>
            </div>
            <div class="typing-indicator" id="typingIndicator">
                <span class="dot"></span><span class="dot"></span><span class="dot"></span>
            </div>
        </div>
        <div class="chat-footer">
            <input type="text" class="chat-input" id="chatInput" placeholder="Tanyakan sejarah Toraja..." onkeypress="handleKeyPress(event)">
            <button class="send-btn" onclick="sendMessage()"><i class="fa-solid fa-paper-plane"></i></button>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Modal Logic
        const toggleBtn = document.getElementById('chatbotToggle');
        const closeBtn = document.getElementById('chatbotClose');
        const panel = document.getElementById('chatbotPanel');

        toggleBtn.addEventListener('click', () => {
            panel.classList.toggle('active');
        });

        closeBtn.addEventListener('click', () => {
            panel.classList.remove('active');
        });

        // Chat Logic
        const chatBox = document.getElementById('chatBox');
        const chatInput = document.getElementById('chatInput');
        const typingIndicator = document.getElementById('typingIndicator');

        function handleKeyPress(e) {
            if (e.key === 'Enter') {
                sendMessage();
            }
        }

        function scrollToBottom() {
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        function sendMessage() {
            const message = chatInput.value.trim();
            if (message === "") return;

            // User msg
            appendMessage('user', message);
            chatInput.value = '';

            // Loading
            chatBox.appendChild(typingIndicator);
            typingIndicator.style.display = "block";
            scrollToBottom();

            const formData = new FormData();
            formData.append('query', message);

            fetch('api/chat.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                typingIndicator.style.display = "none";
                let replyInfo = data.answer;
                if(data.score > 0) {
                    replyInfo += `<span class="score-badge"><i class="fa-solid fa-magnifying-glass-chart"></i> TF-IDF Similarity: ${(data.score * 100).toFixed(2)}%</span>`;
                }
                appendMessage('bot', replyInfo);
            })
            .catch(error => {
                typingIndicator.style.display = "none";
                appendMessage('bot', 'Maaf, NLP Engine sedang gangguan koneksi.');
            });
        }

        function appendMessage(sender, text) {
            const msgDiv = document.createElement('div');
            msgDiv.className = `message ${sender}`;

            const bbl = document.createElement('div');
            bbl.className = 'msg-bubble';
            bbl.innerHTML = text;

            msgDiv.appendChild(bbl);
            chatBox.insertBefore(msgDiv, typingIndicator);
            scrollToBottom();
        }
    </script>
</body>
</html>