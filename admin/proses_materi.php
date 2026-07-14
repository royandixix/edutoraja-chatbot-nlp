<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}

// Fungsi untuk membuat folder upload jika belum ada
$target_dir = "../uploads/";
if (!is_dir($target_dir)) {
    mkdir($target_dir, 0777, true);
}

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action == 'add') {
        $judul = $conn->real_escape_string($_POST['judul']);
        $slug = $conn->real_escape_string($_POST['slug']);
        $konten = $conn->real_escape_string($_POST['konten']);
        $latitude = isset($_POST['latitude']) ? $conn->real_escape_string($_POST['latitude']) : '';
        $longitude = isset($_POST['longitude']) ? $conn->real_escape_string($_POST['longitude']) : '';
        $alamat_map = isset($_POST['alamat_map']) ? $conn->real_escape_string($_POST['alamat_map']) : '';
        
        $gambar_url = "";
        
        // Handle file upload
        if (isset($_FILES["gambar"]) && $_FILES["gambar"]["error"] == 0) {
            $clean_name = preg_replace("/[^a-zA-Z0-9\._-]/", "_", basename($_FILES["gambar"]["name"]));
            $filename = time() . "_materi_" . $clean_name;
            $target_file = $target_dir . $filename;
            
            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                $gambar_url = $conn->real_escape_string("uploads/" . $filename);
            }
        }

        $sql = "INSERT INTO materi (judul, slug, gambar_url, konten, latitude, longitude, alamat_map) VALUES ('$judul', '$slug', '$gambar_url', '$konten', '$latitude', '$longitude', '$alamat_map')";
        if ($conn->query($sql)) {
            $_SESSION['msg'] = "Materi berhasil ditambahkan!";
        } else {
            $_SESSION['msg'] = "Gagal menambahkan materi.";
        }
        header("Location: materi.php");
        exit;
    }

    if ($action == 'edit') {
        $id = (int)$_POST['id'];
        $judul = $conn->real_escape_string($_POST['judul']);
        $slug = $conn->real_escape_string($_POST['slug']);
        $konten = $conn->real_escape_string($_POST['konten']);
        $latitude = isset($_POST['latitude']) ? $conn->real_escape_string($_POST['latitude']) : '';
        $longitude = isset($_POST['longitude']) ? $conn->real_escape_string($_POST['longitude']) : '';
        $alamat_map = isset($_POST['alamat_map']) ? $conn->real_escape_string($_POST['alamat_map']) : '';

        // Check if a new file was uploaded
        if (isset($_FILES["gambar"]) && $_FILES["gambar"]["error"] == 0) {
            $clean_name = preg_replace("/[^a-zA-Z0-9\._-]/", "_", basename($_FILES["gambar"]["name"]));
            $filename = time() . "_materi_" . $clean_name;
            $target_file = $target_dir . $filename;
            
            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                $gambar_url = $conn->real_escape_string("uploads/" . $filename);
                // Update with new image
                $sql = "UPDATE materi SET judul='$judul', slug='$slug', gambar_url='$gambar_url', konten='$konten', latitude='$latitude', longitude='$longitude', alamat_map='$alamat_map' WHERE id=$id";
            }
        } else {
            // Update without changing the image
            $sql = "UPDATE materi SET judul='$judul', slug='$slug', konten='$konten', latitude='$latitude', longitude='$longitude', alamat_map='$alamat_map' WHERE id=$id";
        }

        if ($conn->query($sql)) {
            $_SESSION['msg'] = "Materi berhasil diupdate!";
        } else {
            $_SESSION['msg'] = "Gagal mengupdate materi.";
        }
        header("Location: materi.php");
        exit;
    }

    if ($action == 'delete') {
        $id = (int)$_GET['id'];
        
        // Optionally delete the file from server
        $res = $conn->query("SELECT gambar_url FROM materi WHERE id=$id");
        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            if (strpos($row['gambar_url'], 'http') !== 0) {
                $file_to_delete = "../" . $row['gambar_url'];
                if (file_exists($file_to_delete) && is_file($file_to_delete)) {
                    unlink($file_to_delete);
                }
            }
        }

        $sql = "DELETE FROM materi WHERE id=$id";
        if ($conn->query($sql)) {
            $_SESSION['msg'] = "Materi berhasil dihapus!";
        } else {
            $_SESSION['msg'] = "Gagal menghapus materi.";
        }
        header("Location: materi.php");
        exit;
    }
}
header("Location: materi.php");
