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
        $nama = $conn->real_escape_string($_POST['nama']);
        $slug = $conn->real_escape_string($_POST['slug']);
        $deskripsi = $conn->real_escape_string($_POST['deskripsi']);
        
        $gambar_url = "";
        
        // Handle file upload
        if (isset($_FILES["gambar"]) && $_FILES["gambar"]["error"] == 0) {
            $clean_name = preg_replace("/[^a-zA-Z0-9\._-]/", "_", basename($_FILES["gambar"]["name"]));
            $filename = time() . "_budaya_" . $clean_name;
            $target_file = $target_dir . $filename;
            
            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                $gambar_url = $conn->real_escape_string("uploads/" . $filename);
            }
        }

        $sql = "INSERT INTO budaya (nama, slug, gambar_url, deskripsi) VALUES ('$nama', '$slug', '$gambar_url', '$deskripsi')";
        if ($conn->query($sql)) {
            $_SESSION['msg'] = "Budaya adat berhasil ditambahkan!";
        } else {
            $_SESSION['msg'] = "Gagal menambahkan budaya adat.";
        }
        header("Location: budaya.php");
        exit;
    }

    if ($action == 'edit') {
        $id = (int)$_POST['id'];
        $nama = $conn->real_escape_string($_POST['nama']);
        $slug = $conn->real_escape_string($_POST['slug']);
        $deskripsi = $conn->real_escape_string($_POST['deskripsi']);

        // Check if a new file was uploaded
        if (isset($_FILES["gambar"]) && $_FILES["gambar"]["error"] == 0) {
            $clean_name = preg_replace("/[^a-zA-Z0-9\._-]/", "_", basename($_FILES["gambar"]["name"]));
            $filename = time() . "_budaya_" . $clean_name;
            $target_file = $target_dir . $filename;
            
            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                $gambar_url = $conn->real_escape_string("uploads/" . $filename);
                // Update with new image
                $sql = "UPDATE budaya SET nama='$nama', slug='$slug', gambar_url='$gambar_url', deskripsi='$deskripsi' WHERE id=$id";
            }
        } else {
            // Update without changing the image
            $sql = "UPDATE budaya SET nama='$nama', slug='$slug', deskripsi='$deskripsi' WHERE id=$id";
        }

        if ($conn->query($sql)) {
            $_SESSION['msg'] = "Budaya adat berhasil diupdate!";
        } else {
            $_SESSION['msg'] = "Gagal mengupdate budaya adat.";
        }
        header("Location: budaya.php");
        exit;
    }

    if ($action == 'delete') {
        $id = (int)$_GET['id'];
        
        // Optionally delete the file from server
        $res = $conn->query("SELECT gambar_url FROM budaya WHERE id=$id");
        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $file_to_delete = "../" . $row['gambar_url'];
            if (file_exists($file_to_delete) && is_file($file_to_delete)) {
                unlink($file_to_delete);
            }
        }

        $sql = "DELETE FROM budaya WHERE id=$id";
        if ($conn->query($sql)) {
            $_SESSION['msg'] = "Budaya adat berhasil dihapus!";
        } else {
            $_SESSION['msg'] = "Gagal menghapus budaya adat.";
        }
        header("Location: budaya.php");
        exit;
    }
}
header("Location: budaya.php");
