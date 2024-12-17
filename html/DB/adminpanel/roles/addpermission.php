<?php
// dbcon.php dosyasını dahil et
require_once('../dbcon.php');

// Veritabanına bağlantı kontrolü
if ($connection->connect_error) {
    die("Veritabanı bağlantısı başarısız: " . $connection->connect_error);
}

// Form gönderildiğinde işlemi gerçekleştir
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['yetkiAdi'])) {
    $yetkiAdi = $_POST['yetkiAdi'];

    // Veritabanına yeni yetki ekleme sorgusu
    $insert_query = "INSERT INTO RolePermissions (Permission_Name) VALUES (?)";
    $stmt = $connection->prepare($insert_query);
    $stmt->bind_param("s", $yetkiAdi);

    // Sorguyu çalıştır ve kontrol et
    if ($stmt->execute()) {
        // Başarılıysa kullanıcıyı yönlendir
        echo "<script>window.location.href='/db/adminpanel/roles/listpermissions.php';</script>";
        exit(); // Yönlendirme sonrası işlemi sonlandır
    } else {
        // Hata durumunda kullanıcıya bildirim
        echo "Hata: " . $stmt->error;
    }

    // Veritabanı bağlantısını kapat
    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yetki Ekle</title>
    <?php require('../inc/links.php'); ?>
</head>
<body>
    <?php require('../inc/header.php'); ?>
    
    <!-- Yetki ekleme başlık -->
    <div class="container d-flex justify-content-center align-items-center mt-5">
        <div class="w-50">
            <h3 class="text-center mb-4">Yetki Ekle</h3>
            <!-- Form -->
            <form action="addpermission.php" method="post" class="row row-cols-lg-auto g-3 align-items-center">
                <div class="col-12">
                    <label for="yetkiAdi" class="col-form-label">Yetki Adı:</label>
                </div>
                <div class="col-12">
                    <input type="text" class="form-control" id="yetkiAdi" name="yetkiAdi" placeholder="Yetki Adı" required>
                </div>
                
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Ekle</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>

<?php
// Veritabanı bağlantısını kapat
$connection->close();
?>
