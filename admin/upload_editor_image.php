<?php
session_start();
if (!isset($_SESSION['admin'])) {
    http_response_code(403);
    exit(json_encode(['error' => ['message' => 'Unauthorized']]));
}

if (isset($_FILES['upload']) && $_FILES['upload']['name']) {
    $file = $_FILES['upload']['tmp_name'];
    $file_name = $_FILES['upload']['name'];
    $file_name_array = explode(".", $file_name);
    $extension = end($file_name_array);
    $new_image_name = time() . '_' . rand(1000, 9999) . '.' . $extension;
    $target_dir = "../uploads/editor/";
    
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $allowed_extension = array("jpg", "gif", "png", "jpeg", "webp");
    
    if(in_array(strtolower($extension), $allowed_extension)) {
        move_uploaded_file($file, $target_dir . $new_image_name);
        echo json_encode(['url' => 'uploads/editor/' . $new_image_name]);
    } else {
        echo json_encode(['error' => ['message' => 'Format file tidak didukung.']]);
    }
} else {
    echo json_encode(['error' => ['message' => 'Tidak ada file yang diunggah.']]);
}
?>
