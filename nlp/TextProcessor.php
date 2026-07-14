<?php

/**
 * Class TextProcessor
 * 
 * Implementasi Text Preprocessing, Pembobotan TF-IDF (Sublinear Scaling), 
 * dan Cosine Similarity untuk Tana Toraja Eduwisata Chatbot.
 * 
 * Referensi Metode:
 * - TF-IDF & Cosine Similarity: (Hikmah & Dyah Ariyanti dan Ferry Agus Pratama, 2022)
 * - Cosine Similarity Range & Relevance: (Hariyanto et al., 2023)
 */
class TextProcessor {
    
    // Daftar Stopwords sederhana Bahasa Indonesia
    private $stopwords = [
        "yang", "di", "ke", "dan", "atau", "dari", "untuk", "dengan", "akan", 
        "pada", "ini", "itu", "adalah", "sebuah", "sebagai", "dalam", "bisa", 
        "apa", "siapa", "dimana", "kapan", "mengapa", "bagaimana", "apakah"
    ];

    /**
     * Tahap 1: Text Preprocessing
     * Meliputi: Case Folding (lowercase), Hapus Tanda Baca, Tokenization, 
     * Stopword Removal, dan Stemming Dasar.
     */
    public function preprocess($text) {
        // A. Case Folding (Mengubah teks menjadi huruf kecil semua)
        $text = strtolower($text);
        
        // B. Hapus Tanda Baca (Membersihkan teks dari simbol dan angka non-alphanumeric)
        $text = preg_replace('/[^\p{L}\p{N}\s]/u', '', $text);
        
        // C. Tokenization (Memecah teks menjadi array kata/token berdasarkan spasi)
        $tokens = explode(' ', $text);
        
        // D. Stopword Removal & Stemming (Sederhana)
        $clean_tokens = [];
        foreach ($tokens as $token) {
            $token = trim($token);
            if (!empty($token) && !in_array($token, $this->stopwords)) {
                // Stemming Dasar Bahasa Indonesia untuk mengubah kata berimbuhan menjadi kata dasar
                // Hapus awalan: me-, di-, pe-, ber-, ter-
                $token = preg_replace('/^(mem|men|meng|meny|me|di|pem|pen|peng|peny|pe|ber|ter)/', '', $token);
                // Hapus akhiran: -kan, -i, -an, -nya
                $token = preg_replace('/(kan|i|an|nya)$/', '', $token);
                
                if (strlen($token) > 2) { // Memastikan token kata dasar valid
                    $clean_tokens[] = $token;
                }
            }
        }
        
        return $clean_tokens;
    }

    /**
     * Tahap 2.1: Perhitungan Term Frequency (TF) menggunakan pendekatan Sublinear Scaling.
     * Rumus: TF_dt = 1 + log_10(tf_dt)
     * Keterangan:
     * - TF_dt: Nilai bobot Term Frequency akhir untuk kata t pada dokumen d.
     * - tf_dt: Frekuensi mentah (raw frequency) / jumlah kemunculan kata t dalam dokumen d.
     * - 1: Konstanta untuk memastikan nilai bobot tidak nol jika kata ditemukan.
     */
    public function calculateTF($tokens) {
        $tf = [];
        $total_terms = count($tokens);
        
        if ($total_terms == 0) return $tf;

        // Hitung frekuensi mentah (raw frequency) masing-masing kata t
        $term_counts = array_count_values($tokens);
        foreach ($term_counts as $term => $count) {
            // TF_dt = 1 + log10(tf_dt)
            $tf[$term] = 1 + log10($count);
        }
        
        return $tf;
    }

    /**
     * Tahap 2.2: Perhitungan Inverse Document Frequency (IDF).
     * Rumus: IDF_t = log_10(N / df_t)
     * Keterangan:
     * - IDF_t: Nilai kebalikan frekuensi dokumen untuk kata t.
     * - N: Total jumlah seluruh dokumen informasi pariwisata dalam basis pengetahuan.
     * - df_t: Document Frequency, yaitu jumlah dokumen dalam korpus yang mengandung kata t.
     */
    public function calculateIDF($all_documents) {
        $idf = [];
        $total_documents = count($all_documents); // N
        $term_doc_count = []; // df_t

        // Menghitung Document Frequency (df_t) untuk setiap kata
        foreach ($all_documents as $doc_tokens) {
            $unique_terms = array_unique($doc_tokens);
            foreach ($unique_terms as $term) {
                if (!isset($term_doc_count[$term])) {
                    $term_doc_count[$term] = 0;
                }
                $term_doc_count[$term]++;
            }
        }

        // Menghitung IDF_t = log10(N / df_t) untuk setiap kata t
        foreach ($term_doc_count as $term => $count) {
            $idf[$term] = log10($total_documents / $count);
        }

        return $idf;
    }

    /**
     * Tahap 2.3: Perhitungan Bobot Akhir Kata (TF-IDF).
     * Rumus: W_dt = TF_dt * IDF_t
     * Keterangan:
     * - W_dt: Bobot akhir kata t pada dokumen d.
     */
    public function calculateTFIDF($tf, $idf) {
        $tfidf = [];
        foreach ($tf as $term => $tf_val) {
            $idf_val = isset($idf[$term]) ? $idf[$term] : 0;
            $tfidf[$term] = $tf_val * $idf_val;
        }
        return $tfidf;
    }

    /**
     * Tahap 3: Perhitungan Cosine Similarity antara dua vektor (kueri A dan dokumen B).
     * Rumus: Similarity = cos(θ) = ( ∑(A_i * B_i) ) / ( √∑(A_i^2) * √∑(B_i^2) )
     * Keterangan:
     * - A_i: Bobot kata ke-i pada vektor kueri pengguna (Query).
     * - B_i: Bobot kata ke-i pada vektor dokumen pariwisata (Document).
     * - ∑: Simbol penjumlahan dari seluruh elemen vektor.
     * - √: Simbol akar kuadrat untuk normalisasi panjang vektor.
     */
    public function cosineSimilarity($vec1, $vec2) {
        $dot_product = 0; // ∑(A_i * B_i)
        $norm_vec1 = 0;   // ∑(A_i^2)
        $norm_vec2 = 0;   // ∑(B_i^2)

        // Mendapatkan semua unique terms dari kedua vektor untuk perbandingan
        $all_terms = array_unique(array_merge(array_keys($vec1), array_keys($vec2)));

        foreach ($all_terms as $term) {
            $v1 = isset($vec1[$term]) ? $vec1[$term] : 0; // A_i
            $v2 = isset($vec2[$term]) ? $vec2[$term] : 0; // B_i

            $dot_product += ($v1 * $v2);
            $norm_vec1 += pow($v1, 2);
            $norm_vec2 += pow($v2, 2);
        }

        // Jika salah satu panjang vektor nol, kemiripan adalah 0
        if ($norm_vec1 == 0 || $norm_vec2 == 0) {
            return 0.0;
        }

        // Hasil pembagian dot product dengan perkalian akar normalisasi panjang kedua vektor
        return $dot_product / (sqrt($norm_vec1) * sqrt($norm_vec2));
    }
}
?>
