<?php
require_once 'config.php';

$sql1 = "ALTER TABLE materi ADD COLUMN latitude VARCHAR(50) DEFAULT NULL";
$sql2 = "ALTER TABLE materi ADD COLUMN longitude VARCHAR(50) DEFAULT NULL";
$sql3 = "ALTER TABLE materi ADD COLUMN alamat_map TEXT DEFAULT NULL";

$conn->query($sql1);
$conn->query($sql2);
$conn->query($sql3);

echo "Columns added successfully";
?>
