<?php
// dbcon.php dosyasını dahil et
require_once('../dbcon.php');

// Veritabanı bağlantısı kontrolü
if ($connection->connect_error) {
    die("Veritabanı bağlantısı başarısız: " . $connection->connect_error);
}

// Rol güncelleniyor mu kontrol et
if (isset($_POST['rolAdi']) && isset($_GET['id'])) {
    $role_id = $_GET['id']; // URL'den gelen Role_ID
    $role_name = $_POST['rolAdi']; // Formdan gelen yeni rol adı

    // Veritabanında ilgili rolü güncelle
    $update_sql = "UPDATE Roles SET Role_Name = ? WHERE Role_ID = ?";
    $stmt = $connection->prepare($update_sql);
    $stmt->bind_param("si", $role_name, $role_id);

    if ($stmt->execute()) {
        // Başarıyla güncellendi, listeye geri yönlendir
        echo "<script>window.location.href='/db/adminpanel/roles/listroles.php';</script>";
        exit();
    } else {
        echo "Hata: " . $stmt->error;
    }

    $stmt->close();
} elseif (isset($_GET['id'])) {
    // Eğer id parametresi varsa, düzenlenecek rol bilgilerini al
    $role_id = $_GET['id'];
    $sql = "SELECT Role_Name FROM Roles WHERE Role_ID = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("i", $role_id);
    $stmt->execute();
    $stmt->bind_result($role_name);
    $stmt->fetch();
    $stmt->close();
} else {
    // Eğer id parametresi yoksa, listeye geri dön
    echo "<script>window.location.href='/db/adminpanel/roles/listroles.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rol Güncelleme</title>
    <?php require('../inc/links.php'); ?>
</head>
<body>
    <?php require('../inc/header.php'); ?>
    <!-- Header Alanı -->

    <!-- rol güncelleme başlık -->
    <div class="container d-flex justify-content-center align-items-center mt-5">
        <div class="w-50">
            <h3 class="text-center mb-4">Rol Güncelleme</h3>
            <!-- Form -->
            <form action="updaterole.php?id=<?php echo $role_id; ?>" method="post" class="row row-cols-lg-auto g-3 align-items-center">
                <div class="col-12">
                    <label for="rolAdi" class="col-form-label">Rol Adı:</label>
                </div>
                <div class="col-12">
                    <input type="text" class="form-control" id="rolAdi" name="rolAdi" value="<?php echo $role_name; ?>" placeholder="Rol Adı" required>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Güncelle</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
