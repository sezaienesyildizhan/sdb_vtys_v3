<?php
session_start(); // Oturum başlat

// Veritabanı bağlantısı
$servername = "db";
$username = "root"; // Veritabanı kullanıcı adı
$password = "password"; // Root şifresi
$dbname = "SalesTrackingDB"; // Veritabanı adı

$connection = new mysqli($servername, $username, $password, $dbname);

if (!$connection) {
    die("MySQLi Connection failed: " . mysqli_connect_error());
}

?>

<!-- 
require('dbcon.php'); // Veri tabanı bağlantı dosyası dahil ediliyor.

// Veri tabanı bağlantısı kontrolü
if (!isset($connection) || !$connection) {
    die("Veri tabanı bağlantısı başarısız: " . mysqli_connect_error());
}

require('inc/header.php'); // Header dosyası dahil ediliyor. -->