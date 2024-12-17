<?php
require('../dbcon.php'); // Veritabanı bağlantısı
mysqli_set_charset($connection, "utf8mb4"); // Karakter seti

// ID ve işlem türünü al
if (!isset($_POST['id']) || !isset($_POST['action']) || !is_numeric($_POST['id'])) {
    die(json_encode(['success' => false, 'message' => 'Geçersiz parametreler']));
}

$courier_id = intval($_POST['id']);
$action = $_POST['action']; // 'activate' veya 'deactivate'

// İşlem türüne göre SQL sorgusunu hazırla
if ($action === 'activate') {
    $query = "UPDATE Couriers SET Courier_IsActive = 1 WHERE Courier_ID = $courier_id";
} elseif ($action === 'deactivate') {
    $query = "UPDATE Couriers SET Courier_IsActive = 0 WHERE Courier_ID = $courier_id";
} else {
    die(json_encode(['success' => false, 'message' => 'Geçersiz işlem']));
}

// Sorguyu çalıştır ve sonucu döndür
if (mysqli_query($connection, $query)) {
    echo json_encode(['success' => true, 'message' => 'Kurye durumu güncellendi.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Veritabanı hatası: ' . mysqli_error($connection)]);
}

// Veritabanı bağlantısını kapat
mysqli_close($connection);
?>
