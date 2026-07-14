<?php
header('Content-Type: application/json');

require_once '../config.php';
require_once '../nlp/TextProcessor.php';

$query = isset($_POST['query']) ? $_POST['query'] : '';

if (empty(trim($query))) {
    echo json_encode(['answer' => 'Pertanyaan kosong.', 'score' => 0]);
    exit;
}

$processor = new TextProcessor();

// Ambil knowledge base manual
$sql = "SELECT id, question, answer, maps_url FROM knowledge_base";
$result = $conn->query($sql);

$documents = [];
$map_id_answer = [];

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $key = 'kb_' . $row['id'];
        $documents[$key] = $processor->preprocess($row['question']);
        
        $final_answer = $row['answer'];
        if (!empty($row['maps_url'])) {
            $final_answer .= "<br><br><a href='" . htmlspecialchars($row['maps_url'], ENT_QUOTES) . "' target='_blank' style='display: inline-block; padding: 8px 12px; background-color: #f59e0b; color: white; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 0.9em;'><i class='fa-solid fa-location-dot'></i> Buka Penunjuk Arah di Maps</a>";
        }
        $map_id_answer[$key] = $final_answer;
    }
}

// Ambil data Destinasi
$res_dest = $conn->query("SELECT id, nama, deskripsi, slug FROM destinasi");
if ($res_dest && $res_dest->num_rows > 0) {
    while($row = $res_dest->fetch_assoc()) {
        $key = 'dest_' . $row['id'];
        $doc_text = $row['nama'] . " " . $row['deskripsi'];
        $documents[$key] = $processor->preprocess($doc_text);
        $map_id_answer[$key] = "Terkait destinasi <b>" . $row['nama'] . "</b>: " . $row['deskripsi'] . "<br><br><a href='materi.php?id=".$row['slug']."' style='color:#f59e0b; font-weight:bold;'><i class='fa-solid fa-arrow-right'></i> Baca Materi Lengkapnya</a>";
    }
}

// Ambil data Budaya
$res_bud = $conn->query("SELECT id, nama, deskripsi, slug FROM budaya");
if ($res_bud && $res_bud->num_rows > 0) {
    while($row = $res_bud->fetch_assoc()) {
        $key = 'bud_' . $row['id'];
        $doc_text = $row['nama'] . " " . $row['deskripsi'];
        $documents[$key] = $processor->preprocess($doc_text);
        $map_id_answer[$key] = "Terkait budaya <b>" . $row['nama'] . "</b>: " . $row['deskripsi'] . "<br><br><a href='materi.php?id=".$row['slug']."' style='color:#f59e0b; font-weight:bold;'><i class='fa-solid fa-arrow-right'></i> Baca Materi Lengkapnya</a>";
    }
}

// Ambil data Materi (Halaman Panjang)
$res_mat = $conn->query("SELECT id, judul, konten, slug FROM materi");
if ($res_mat && $res_mat->num_rows > 0) {
    while($row = $res_mat->fetch_assoc()) {
        $key = 'mat_' . $row['id'];
        $clean_konten = strip_tags($row['konten']);
        // Batasi panjang konten agar tokenizing tidak terlalu berat, ambil judul + 1000 karakter pertama
        $doc_text = $row['judul'] . " " . substr($clean_konten, 0, 1000);
        $documents[$key] = $processor->preprocess($doc_text);
        
        $summary = substr($clean_konten, 0, 150) . "...";
        $map_id_answer[$key] = "Mengenai <b>" . $row['judul'] . "</b>:<br>" . $summary . "<br><br><a href='materi.php?id=".$row['slug']."' style='color:#f59e0b; font-weight:bold;'><i class='fa-solid fa-arrow-right'></i> Baca Materi Lengkapnya</a>";
    }
}

if (empty($documents)) {
    echo json_encode(['answer' => 'Maaf, basis pengetahuan sistem sedang kosong.', 'score' => 0]);
    exit;
}

// Tambahkan query user ke daftar dokumen untuk dihitung bareng IDF (opsional, tapi umum agar term ada di global dict)
$user_tokens = $processor->preprocess($query);

// Gabungkan semua dokumen
$all_documents = $documents;
$all_documents['query'] = $user_tokens;

// Hitung IDF untuk semua terms
$idf = $processor->calculateIDF($all_documents);

// Hitung TF-IDF untuk query user
$query_tf = $processor->calculateTF($user_tokens);
$query_tfidf = $processor->calculateTFIDF($query_tf, $idf);

// Hitung Cosine Similarity untuk tiap dokumen DB against query
$highest_score = 0;
$best_id = null;

foreach ($documents as $doc_id => $doc_tokens) {
    $doc_tf = $processor->calculateTF($doc_tokens);
    $doc_tfidf = $processor->calculateTFIDF($doc_tf, $idf);
    
    $score = $processor->cosineSimilarity($query_tfidf, $doc_tfidf);
    
    if ($score > $highest_score) {
        $highest_score = $score;
        $best_id = $doc_id;
    }
}

// Threshold
$threshold = 0.2; // Bisa disesuaikan
$response_text = "";

if ($best_id !== null && $highest_score >= $threshold) {
    $response_text = $map_id_answer[$best_id];
} else {
    $response_text = "Maaf, saya tidak menemukan jawaban yang sesuai.";
    $highest_score = 0;
}

// Log chat ke database (opsional fiturnya, ditambahkan sesuai spec)
$stmt = $conn->prepare("INSERT INTO chat_logs (user_input, bot_response, similarity_score) VALUES (?, ?, ?)");
$stmt->bind_param("ssd", $query, $response_text, $highest_score);
$stmt->execute();
$stmt->close();

echo json_encode([
    'answer' => $response_text,
    'score' => $highest_score
]);

$conn->close();
?>
