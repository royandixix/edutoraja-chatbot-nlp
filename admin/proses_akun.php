<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}

// Cek dan tambahkan kolom foto_url jika belum ada
$check_column = $conn->query("SHOW COLUMNS FROM users LIKE 'foto_url'");
if ($check_column->num_rows == 0) {
    $conn->query("ALTER TABLE users ADD COLUMN foto_url VARCHAR(255) DEFAULT NULL");
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action == 'update_profile') {
    $current_username = $_SESSION['admin'];
    $new_username = trim($_POST['username']);
    $new_password = $_POST['password'];

    // Ambil data user saat ini
    $stmt = $conn->prepare("SELECT id, foto_url FROM users WHERE username = ?");
    $stmt->bind_param("s", $current_username);
    $stmt->execute();
    $current_user = $stmt->get_result()->fetch_assoc();
    $current_user_id = $current_user['id'];
    $old_foto_url = $current_user['foto_url'];
    $stmt->close();

    if (empty($new_username)) {
        $_SESSION['msg_error'] = "Username tidak boleh kosong!";
        $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'dashboard.php';
        header("Location: " . $redirect);
        exit;
    }

    // Cek apakah username baru sudah digunakan oleh user lain
    if ($new_username !== $current_username) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
        $stmt->bind_param("si", $new_username, $current_user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $_SESSION['msg_error'] = "Username '$new_username' sudah digunakan!";
            $stmt->close();
            $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'dashboard.php';
            header("Location: " . $redirect);
            exit;
        }
        $stmt->close();
    }

    // Proses upload foto jika ada file yang diunggah
    $foto_path = $old_foto_url;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['foto']['tmp_name'];
        $file_name = $_FILES['foto']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        $allowed_exts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (in_array($file_ext, $allowed_exts)) {
            // Buat nama file unik
            $new_file_name = "admin_profile_" . $current_user_id . "_" . time() . "." . $file_ext;
            $upload_dir = "../uploads/";
            
            // Pastikan folder uploads ada
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $target_path = $upload_dir . $new_file_name;
            if (move_uploaded_file($file_tmp, $target_path)) {
                $foto_path = "uploads/" . $new_file_name;
                
                // Hapus foto profil lama jika ada dan filenya ada
                if (!empty($old_foto_url) && file_exists("../" . $old_foto_url)) {
                    unlink("../" . $old_foto_url);
                }
            } else {
                $_SESSION['msg_error'] = "Gagal mengunggah foto profil!";
                $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'dashboard.php';
                header("Location: " . $redirect);
                exit;
            }
        } else {
            $_SESSION['msg_error'] = "Format foto tidak didukung! Gunakan JPG, JPEG, PNG, WEBP, atau GIF.";
            $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'dashboard.php';
            header("Location: " . $redirect);
            exit;
        }
    }

    // Update profil di database
    if (!empty($new_password)) {
        $hashed_password = md5($new_password);
        $stmt = $conn->prepare("UPDATE users SET username = ?, password = ?, foto_url = ? WHERE id = ?");
        $stmt->bind_param("sssi", $new_username, $hashed_password, $foto_path, $current_user_id);
    } else {
        $stmt = $conn->prepare("UPDATE users SET username = ?, foto_url = ? WHERE id = ?");
        $stmt->bind_param("ssi", $new_username, $foto_path, $current_user_id);
    }

    if ($stmt->execute()) {
        $_SESSION['admin'] = $new_username;
        $_SESSION['msg'] = "Profil berhasil diperbarui!";
    } else {
        $_SESSION['msg_error'] = "Gagal memperbarui profil!";
    }
    $stmt->close();
}
elseif ($action == 'change_other_password') {
    $user_id = $_POST['user_id'];
    $new_password = $_POST['password'];

    if (empty($new_password)) {
        $_SESSION['msg_error'] = "Password tidak boleh kosong!";
        $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'dashboard.php';
        header("Location: " . $redirect);
        exit;
    }

    $hashed_password = md5($new_password);
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hashed_password, $user_id);
    if ($stmt->execute()) {
        $_SESSION['msg'] = "Password pengguna lain berhasil diperbarui!";
    } else {
        $_SESSION['msg_error'] = "Gagal memperbarui password pengguna!";
    }
    $stmt->close();
}
elseif ($action == 'delete_user') {
    $user_id = $_GET['id'];
    $current_username = $_SESSION['admin'];

    // Ambil data user saat ini dan foto_url yang akan dihapus
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $current_username);
    $stmt->execute();
    $current_user_id = $stmt->get_result()->fetch_assoc()['id'];
    $stmt->close();

    if ($user_id == $current_user_id) {
        $_SESSION['msg_error'] = "Anda tidak dapat menghapus akun Anda sendiri!";
        $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'dashboard.php';
        header("Location: " . $redirect);
        exit;
    }

    // Ambil foto_url untuk dihapus dari file system
    $stmt = $conn->prepare("SELECT foto_url FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $target_foto = $stmt->get_result()->fetch_assoc()['foto_url'];
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    if ($stmt->execute()) {
        if (!empty($target_foto) && file_exists("../" . $target_foto)) {
            unlink("../" . $target_foto);
        }
        $_SESSION['msg'] = "Akun pengguna berhasil dihapus!";
    } else {
        $_SESSION['msg_error'] = "Gagal menghapus akun pengguna!";
    }
    $stmt->close();
}

$redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'dashboard.php';
header("Location: " . $redirect);
exit;
?>
