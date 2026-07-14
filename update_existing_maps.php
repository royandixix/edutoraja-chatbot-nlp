<?php
require_once 'config.php';

$coords_map = [
    'londa' => [
        'lat' => -3.013589, 
        'lng' => 119.882939, 
        'address' => 'Londa, Sandan Uai, Sanggalangi, Kabupaten Toraja Utara'
    ],
    'kete_kesu' => [
        'lat' => -2.990425, 
        'lng' => 119.904245, 
        'address' => 'Desa Adat Kete Kesu, Bonoran, Tikala, Kabupaten Toraja Utara'
    ],
    'batutumonga' => [
        'lat' => -2.913075, 
        'lng' => 119.887431, 
        'address' => 'Batutumonga, Sesean Suloara, Kabupaten Toraja Utara'
    ]
];

foreach ($coords_map as $slug => $data) {
    $lat = $data['lat'];
    $lng = $data['lng'];
    $address = $conn->real_escape_string($data['address']);
    $conn->query("UPDATE materi SET latitude='$lat', longitude='$lng', alamat_map='$address' WHERE slug='$slug'");
}

echo "Existing maps updated successfully";
?>
