<?php
// Oturum başlat


// Veritabanı bağlantısı
require_once('../dbcon.php');

// Form verisi gönderildiğinde
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Rol adı verisini al
    $rolAdi = $_POST['rolAdi'];

    // SQL sorgusunu hazırla
    $sql = "INSERT INTO Roles (Role_Name) VALUES (?)";

    // Prepared statement kullanarak sorguyu çalıştır
    if ($stmt = $connection->prepare($sql)) {
        $stmt->bind_param("s", $rolAdi);  // "s" string parametre olarak bağlanacak
        $stmt->execute(); // Sorguyu çalıştır

        // Eğer sorgu başarılı olduysa
        if ($stmt->affected_rows > 0) {
            echo "<script>alert('Rol başarıyla eklendi.'); window.location.href = 'listroles.php';</script>";
        } else {
            echo "<script>alert('Rol eklenirken bir hata oluştu.');</script>";
        }

        // Prepared statement'i kapat
        $stmt->close();
    } else {
        echo "<script>alert('Sorgu hazırlanırken bir hata oluştu.');</script>";
    }
}

// Veritabanı bağlantısını kapat
$connection->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rol Ekle</title>
    <?php require('../inc/links.php'); ?>
</head>
<body>
    <?php require('../inc/header.php'); ?>
    <!-- Header Alanı -->

    <!-- rol ekleme başlık -->

    <!-- rol ismi için textbox -->
    <div class="container d-flex justify-content-center align-items-center mt-5">
        <div class="w-50">
            <h3 class="text-center mb-4">Rol Ekle</h3>
            <!-- Form -->
            <form action="addrole.php" method="post" class="row row-cols-lg-auto g-3 align-items-center">
                <div class="col-12">
                    <label for="rolAdi" class="col-form-label">Rol Adı:</label>
                </div>
                <div class="col-12">
                    <input type="text" class="form-control" id="rolAdi" name="rolAdi" placeholder="Rol Adı" required>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Ekle</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
