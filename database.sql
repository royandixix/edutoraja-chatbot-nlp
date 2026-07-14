CREATE DATABASE IF NOT EXISTS chatbot_toraja;
USE chatbot_toraja;

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `users` (`id`, `username`, `password`) VALUES
(1, 'admin', '0192023a7bbd73250516f069df18b500'); -- password: admin123 (MD5 used for simplicity, usually bcrypt but MD5 is easier for simple script without setup)

CREATE TABLE IF NOT EXISTS `knowledge_base` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` text NOT NULL,
  `answer` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `knowledge_base` (`id`, `question`, `answer`) VALUES
(1, 'Apa itu Tana Toraja?', 'Tana Toraja adalah salah satu kabupaten di provinsi Sulawesi Selatan, Indonesia, yang terkenal dengan budaya dan adat istiadatnya yang unik, terutama upacara pemakamannya.'),
(2, 'Dimana letak Kete Kesu?', 'Kete Kesu adalah sebuah desa wisata yang terletak sekitar 4 km dari Rantepao, Toraja Utara, terkenal dengan deretan Tongkonan (rumah adat Toraja) dan kuburan gantung yang tua.'),
(3, 'Apa itu upacara Rambu Solo?', 'Rambu Solo adalah upacara pemakaman adat Toraja yang sangat meriah dan membutuhkan biaya besar, melibatkan pemotongan kerbau dan babi sebagai bentuk penghormatan kepada orang yang meninggal.'),
(4, 'Apa makanan khas Toraja?', 'Beberapa makanan khas Toraja antara lain Pa\'piong (makanan yang dimasak dalam bambu), Deppa Tori (kue tradisional), dan Pantollo Pamarrasan.'),
(5, 'Kapan waktu terbaik berkunjung ke Toraja?', 'Waktu terbaik adalah sekitar bulan Juli hingga Agustus, saat banyak upacara Rambu Solo diadakan, dan cuaca umumnya cerah.'),
(6, 'Apa itu Londa? Kuburan goa londa', 'Londa adalah objek wisata kuburan goa alam di Toraja. Di sini, peti mati diletakkan di celah-celah batu karang atau tebing berserta patung kayu (Tau-tau) yang menyerupai wajah orang yang meninggal.'),
(7, 'Apa itu Lemo? Makam tebing lemo', 'Lemo adalah objek wisata berupa kuburan batu di tebing curam. Tebing ini dipahat secara manual untuk membuat liang lahat. Menariknya, terdapat puluhan Tau-tau (patung arwah) yang berdiri berjajar menyerupai penonton di balkon.'),
(8, 'Bagaimana cuaca atau iklim di Toraja?', 'Karena letaknya di dataran tinggi (pegunungan), cuaca di Toraja umumnya sejuk dan dingin, berkisar antara 16°C hingga 28°C. Disarankan membawa jaket atau pakaian tebal jika berkunjung pada malam hari.'),
(9, 'Apa pakaian adat Tana Toraja?', 'Pakaian adat Toraja untuk laki-laki disebut Seppa Tallung Buku, yang khas dengan celana panjang sebentuk lutut berhias motif tenun. Sedangkan untuk perempuan disebut Baju Pokko, berbentuk lengan pendek dengan warna mencolok dilengkapi manik-manik (Kandaure).'),
(10, 'Dimana letak Batutumonga? Negeri di atas awan', 'Batutumonga terletak di lereng Gunung Sesean, Toraja Utara. Tempat ini sering dijuluki "Negeri di Atas Awan Toraja" karena pada pagi hari hamparan awan putih menutupi lembah persawahan Rantepao di bawahnya.'),
(11, 'Apa minuman khas Toraja yang terkenal?', 'Kopi Toraja adalah minuman yang paling mendunia. Jenis Kopi Arabika dari Toraja sangat disukai karena memiliki tingkat keasaman yang rendah dan aroma rempah-soklat yang khas.'),
(12, 'Apa itu Bori Parinding? Megalitikum bori', 'Bori Parinding adalah kompleks situs megalitikum yang berisi lebih dari 100 menhir (batu berdiri) dari berbagai ukuran. Bori digunakan sebagai tempat pelaksaan ritual upacara pemakaman khusus bagi kaum bangsawan tingkat tinggi (raja/pemangku adat).');

CREATE TABLE IF NOT EXISTS `chat_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_input` text NOT NULL,
  `bot_response` text NOT NULL,
  `similarity_score` float NOT NULL DEFAULT '0',
  `timestamp` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `destinasi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(150) NOT NULL,
  `deskripsi` text NOT NULL,
  `gambar_url` varchar(255) NOT NULL,
  `slug` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `destinasi` (`id`, `nama`, `deskripsi`, `gambar_url`, `slug`) VALUES
(1, 'Makam Goa Londa', 'Situs pemakaman bersejarah di tebing batu kapur, tempat menyimpan peti mati berumur ratusan tahun lengkap dengan Tau-Tau.', 'https://images.unsplash.com/photo-1542614948-2b8744078864?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80', 'londa'),
(2, 'Desa Kete Kesu', 'Kompleks rumah adat tradisional Tongkonan tertua di Toraja. Tempat terbaik untuk mempelajari ukiran kayu asli Toraja.', 'https://images.unsplash.com/photo-1691129528659-dc7a07727196?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80', 'kete_kesu'),
(3, 'Batutumonga', 'Sensasi berada di "Negeri di Atas Awan". Menawarkan panorama luar biasa persawahan dan kota Rantepao dari lereng Gunung Sesean.', 'https://plus.unsplash.com/premium_photo-1661882042079-cdaba1d5d590?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80', 'batutumonga');

CREATE TABLE IF NOT EXISTS `budaya` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(150) NOT NULL,
  `deskripsi` text NOT NULL,
  `gambar_url` varchar(255) NOT NULL,
  `slug` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `budaya` (`id`, `nama`, `deskripsi`, `gambar_url`, `slug`) VALUES
(1, 'Upacara Rambu Solo\'', 'Rambu Solo\' adalah upacara pemakaman adat terpenting dan sakral. Membutuhkan prosesi berhari-hari serta pengorbanan babi dan kerbau belang.', 'https://images.unsplash.com/photo-1707923485764-58e17b8f97de?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80', 'rambu_solo'),
(2, 'Arsitektur Tongkonan', 'Tongkonan berfungsi bukan sekadar rumah tempat tinggal, melainkan pusat komunal religius. Atap melengkungnya menandakan status sosial pemiliknya.', 'https://images.unsplash.com/photo-1628107954931-50e50f39385d?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80', 'tongkonan');

CREATE TABLE IF NOT EXISTS `materi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `judul` varchar(150) NOT NULL,
  `konten` text NOT NULL,
  `gambar_url` varchar(255) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `latitude` varchar(50) DEFAULT NULL,
  `longitude` varchar(50) DEFAULT NULL,
  `alamat_map` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `materi` (`id`, `judul`, `konten`, `gambar_url`, `slug`) VALUES
(1, 'Upacara Rambu Solo\'', '<p style=\"font-size: 1.1rem; color: #555; font-weight: 500;\">Rambu Solo\' bukan sekadar upacara kematian, melainkan pesta perayaan mengantarkan arwah leluhur Toraja (Aluk To Dolo) menuju keabadian di alam Puya.</p><hr style=\"margin: 25px 0; border: 1px solid #eee;\"><h3 style=\"color: #d97706;\"><i class=\"fa-solid fa-clock-rotate-left\"></i> Filosofi Kematian</h3><p>Masyarakat Toraja percaya bahwa seseorang belum dianggap benar-benar meninggal sebelum seluruh prosesi Rambu Solo\' dituntaskan; hingga saat itu, jenazah hanya dianggap seperti orang sakit (Toma Kula\').</p><h3 style=\"color: #d97706;\"><i class=\"fa-solid fa-star\"></i> Prosesi Utama</h3><ul><li><b>Ma\'Tinggoro Tedong:</b> Penyembelihan kerbau belang dengan satu tebasan untuk melepaskan arwahnya.</li><li><b>Ma\'Badik:</b> Tarian duka cita para pria yang membentuk lingkaran.</li><li><b>Mapasilaga Tedong:</b> Tradisi adu kerbau yang sangat epik sebelum upacara puncak.</li></ul><div style=\"background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin: 25px 0; border-radius: 4px;\"><h4 style=\"margin-top: 0; color: #b45309;\"><i class=\"fa-solid fa-lightbulb\"></i> Tahukah Anda?</h4><p style=\"margin-bottom: 0; color: #78350f;\">Upacara ini membutuhkan biaya dari puluhan juta hingga miliaran rupiah karena harga seekor Tedong Bonga (kerbau belang) bisa mencapai harga sebuah mobil mewah!</p></div><h3 style=\"color: #d97706;\"><i class=\"fa-solid fa-location-dot\"></i> Etika Berkunjung</h3><p>Jangan pernah melintas tepat di depan rombongan yang sedang mengarak jenazah, dan selalu kenakan pakaian berwarna gelap (hitam) sebagai tanda duka cita dan penghormatan.</p>', 'https://images.unsplash.com/photo-1707923485764-58e17b8f97de?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80', 'rambu_solo'),
(2, 'Arsitektur Tongkonan', '<p style=\"font-size: 1.1rem; color: #555; font-weight: 500;\">Tongkonan adalah rumah adat sekaligus simbol derajat kebangsawanan dan pusat komunal religius bagi masyarakat Toraja.</p><hr style=\"margin: 25px 0; border: 1px solid #eee;\"><h3 style=\"color: #d97706;\"><i class=\"fa-solid fa-clock-rotate-left\"></i> Asal Usul Nama</h3><p>Berasal dari kata \"tongkon\" yang berarti \"duduk bersama-sama\". Rumah ini tidak dimiliki secara individu, melainkan diwariskan turun-temurun oleh keluarga besar atau marga.</p><h3 style=\"color: #d97706;\"><i class=\"fa-solid fa-star\"></i> Daya Tarik Utama</h3><ul><li><b>Atap Perahu:</b> Bentuk atap melengkung menjulang ke atas menyerupai perahu leluhur yang tiba di Sulawesi.</li><li><b>Tanduk Kerbau:</b> Jumlah tanduk kerbau (kabongo) yang dipasang berderet di tiang utama menandakan strata sosial pemiliknya.</li><li><b>Ukiran Pa\'ssura:</b> Memiliki 4 warna dasar (hitam, merah, kuning, putih) dengan ukiran motif hewan atau alam yang mengandung makna magis.</li></ul><div style=\"background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin: 25px 0; border-radius: 4px;\"><h4 style=\"margin-top: 0; color: #b45309;\"><i class=\"fa-solid fa-lightbulb\"></i> Tahukah Anda?</h4><p style=\"margin-bottom: 0; color: #78350f;\">Semua rumah Tongkonan pasti dihadapkan menghadap ke arah Utara. Hal ini karena orang Toraja percaya bahwa arah Utara adalah tempat bersemayamnya Sang Pencipta (Puang Matua).</p></div>', 'https://images.unsplash.com/photo-1628107954931-50e50f39385d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80', 'tongkonan'),
(3, 'Makam Goa Londa', '<p style=\"font-size: 1.1rem; color: #555; font-weight: 500;\">Makam Goa Londa adalah kuburan alam berupa goa kapur yang menjadi tempat peristirahatan terakhir para bangsawan Toraja bersama patung-patung kayu penjiwa (Tau-Tau).</p><hr style=\"margin: 25px 0; border: 1px solid #eee;\"><h3 style=\"color: #d97706;\"><i class=\"fa-solid fa-clock-rotate-left\"></i> Sejarah Londa</h3><p>Makam goa ini sudah digunakan sejak abad ke-15. Peti mati (Erong) yang usianya ratusan tahun tidak dikubur, melainkan digantung atau diselipkan di celah-celah tebing goa yang curam.</p><h3 style=\"color: #d97706;\"><i class=\"fa-solid fa-star\"></i> Daya Tarik Utama</h3><ul><li><b>Goa Penuh Misteri:</b> Anda bisa masuk ke dalam goa sedalam 1 km yang dipenuhi tulang belulang dan tengkorak berusia ratusan tahun.</li><li><b>Tau-Tau:</b> Ratusan patung kayu seukuran manusia yang diukir menyerupai wajah jenazah, berjejer di balkon tebing.</li><li><b>Erong:</b> Peti mati kuno berbentuk kerbau atau babi yang digantung tanpa paku.</li></ul><div style=\"background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin: 25px 0; border-radius: 4px;\"><h4 style=\"margin-top: 0; color: #b45309;\"><i class=\"fa-solid fa-lightbulb\"></i> Tahukah Anda?</h4><p style=\"margin-bottom: 0; color: #78350f;\">Posisi peti jenazah di dalam goa dan di tebing ditentukan berdasarkan strata sosialnya. Semakin tinggi kasta sang jenazah, semakin tinggi pula letak peti dan patung Tau-Taunya di tebing!</p></div><h3 style=\"color: #d97706;\"><i class=\"fa-solid fa-location-dot\"></i> Tips Berkunjung & Etika</h3><ul><li>Jangan pernah mengambil atau memindahkan tengkorak, tulang, atau barang apapun dari dalam goa.</li><li>Wajib menyewa lampu petromak dari pemandu lokal (guide) karena di dalam goa sangat gelap dan jalurnya berbatu.</li></ul>', 'https://images.unsplash.com/photo-1542614948-2b8744078864?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80', 'londa'),
(4, 'Desa Kete Kesu', '<p style=\"font-size: 1.1rem; color: #555; font-weight: 500;\">Kete Kesu adalah desa adat tertua di Toraja yang paling ikonik, menampilkan deretan Tongkonan berusia lebih dari 300 tahun yang berbaris rapi bagai lukisan.</p><hr style=\"margin: 25px 0; border: 1px solid #eee;\"><h3 style=\"color: #d97706;\"><i class=\"fa-solid fa-clock-rotate-left\"></i> Asal Usul Kete Kesu</h3><p>Ditetapkan sebagai cagar budaya Toraja, desa ini konon telah berdiri sejak 400 tahun yang lalu dan masih mempertahankan tata letak desa leluhur purba Toraja secara utuh.</p><h3 style=\"color: #d97706;\"><i class=\"fa-solid fa-star\"></i> Daya Tarik Utama</h3><ul><li><b>Deretan Tongkonan:</b> Terdapat 6 Tongkonan utama beserta lumbung padi (Alang) yang saling berhadapan membentuk jalur desa yang epik.</li><li><b>Kuburan Gantung:</b> Di bagian belakang desa terdapat tebing kars yang digunakan sebagai kuburan gantung kuno yang dipenuhi erong (peti mati).</li><li><b>Pusat Ukiran Kayu:</b> Desa ini terkenal sebagai penghasil kerajinan ukiran kayu (Pa\'ssura) terbaik se-Toraja.</li></ul><div style=\"background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin: 25px 0; border-radius: 4px;\"><h4 style=\"margin-top: 0; color: #b45309;\"><i class=\"fa-solid fa-lightbulb\"></i> Tahukah Anda?</h4><p style=\"margin-bottom: 0; color: #78350f;\">Lumbung padi (Alang) di depan Tongkonan memiliki tiang yang terbuat dari kayu Banga (sejenis palem) yang sangat licin. Hal ini disengaja agar tikus tidak bisa memanjat dan memakan padi simpanan warga!</p></div>', 'https://images.unsplash.com/photo-1691129528659-dc7a07727196?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80', 'kete_kesu'),
(5, 'Batutumonga', '<p style=\"font-size: 1.1rem; color: #555; font-weight: 500;\">Berada di lereng Gunung Sesean, Batutumonga menawarkan lanskap magis bak lukisan alam. Tempat ini dijuluki \"Negeri di Atas Awan Tana Toraja\".</p><hr style=\"margin: 25px 0; border: 1px solid #eee;\"><h3 style=\"color: #d97706;\"><i class=\"fa-solid fa-clock-rotate-left\"></i> Letak Geografis</h3><p>Berada di ketinggian sekitar 1300 meter di atas permukaan laut (mdpl) di Kabupaten Toraja Utara. Hawa sejuk dan kabut pagi menjadikan tempat ini surga bagi para pecinta alam.</p><h3 style=\"color: #d97706;\"><i class=\"fa-solid fa-star\"></i> Daya Tarik Utama</h3><ul><li><b>Samudera Awan:</b> Datanglah pada pukul 5 hingga 7 pagi untuk menyaksikan hamparan awan putih menutupi lembah kota Rantepao di bawah Anda.</li><li><b>Terasering Sawah:</b> Pemandangan persawahan berundak yang tidak kalah dengan Ubud, Bali.</li><li><b>Kopi Toraja Toraja Asli:</b> Anda dapat menikmati secangkir kopi arabika asli hasil panen warga Sesean sembari melihat matahari terbit.</li></ul><div style=\"background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin: 25px 0; border-radius: 4px;\"><h4 style=\"margin-top: 0; color: #b45309;\"><i class=\"fa-solid fa-lightbulb\"></i> Tahukah Anda?</h4><p style=\"margin-bottom: 0; color: #78350f;\">Batutumonga adalah jalur utama bagi para pendaki yang ingin menuju puncak Gunung Sesean. Di sekitar jalan raya Batutumonga juga banyak terdapat kuburan batu tua yang terpahat di batu-batu karang raksasa.</p></div><h3 style=\"color: #d97706;\"><i class=\"fa-solid fa-location-dot\"></i> Tips Berkunjung</h3><p>Bawa jaket tebal karena udara pagi hari di lereng Gunung Sesean sangat menusuk tulang (bisa mencapai 14 derajat celcius). Gunakan kendaraan dalam kondisi prima karena medannya sangat menanjak.</p>', 'https://plus.unsplash.com/premium_photo-1661882042079-cdaba1d5d590?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80', 'batutumonga');
