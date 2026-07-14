<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['admin'])) {
    header("Location: index.php");
    exit;
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

if ($action == 'add') {
    $question = $_POST['question'];
    $answer = $_POST['answer'];
    $maps_url = isset($_POST['maps_url']) ? $_POST['maps_url'] : null;
    
    $stmt = $conn->prepare("INSERT INTO knowledge_base (question, answer, maps_url) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $question, $answer, $maps_url);
    if($stmt->execute()) {
        $_SESSION['msg'] = "Data berhasil ditambahkan!";
    }
    $stmt->close();
}
elseif ($action == 'edit') {
    $id = $_POST['id'];
    $question = $_POST['question'];
    $answer = $_POST['answer'];
    $maps_url = isset($_POST['maps_url']) ? $_POST['maps_url'] : null;
    
    $stmt = $conn->prepare("UPDATE knowledge_base SET question=?, answer=?, maps_url=? WHERE id=?");
    $stmt->bind_param("sssi", $question, $answer, $maps_url, $id);
    if($stmt->execute()) {
        $_SESSION['msg'] = "Data berhasil diupdate!";
    }
    $stmt->close();
}
elseif ($action == 'delete') {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM knowledge_base WHERE id=?");
    $stmt->bind_param("i", $id);
    if($stmt->execute()) {
        $_SESSION['msg'] = "Data berhasil dihapus!";
    }
    $stmt->close();
}

header("Location: knowledge_base.php");
exit;
?>
