<?php
// dbcon.php dosyasını dahil et
require_once('../dbcon.php');

// Veritabanına bağlantı kontrolü
if ($connection->connect_error) {
    die("Veritabanı bağlantısı başarısız: " . $connection->connect_error);
}

// Eğer ID parametresi URL'den geliyorsa, veritabanından veriyi çek
if (isset($_GET['id'])) {
    $permission_id = $_GET['id'];

    // Permission_ID ile RolePermissions tablosundan ilgili kaydı çek
    $select_query = "SELECT Permission_Name FROM RolePermissions WHERE Permission_ID = ?";
    $stmt = $connection->prepare($select_query);
    $stmt->bind_param("i", $permission_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Eğer ilgili yetki bulunduysa, formu doldur
    if ($row = $result->fetch_assoc()) {
        $permission_name = $row['Permission_Name'];
    } else {
        die("Yetki bulunamadı.");
    }
} else {
    die("Geçersiz istek.");
}

// Eğer form gönderildiyse, veritabanını güncelle
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['yetkiAdi'])) {
    $yetkiAdi = $_POST['yetkiAdi'];

    // Veritabanında güncelleme sorgusu
    $update_query = "UPDATE RolePermissions SET Permission_Name = ? WHERE Permission_ID = ?";
    $stmt = $connection->prepare($update_query);
    $stmt->bind_param("si", $yetkiAdi, $permission_id);

    if ($stmt->execute()) {
        // Başarıyla güncellendiyse, kullanıcıyı yönlendir
        echo "<script>window.location.href='/db/adminpanel/roles/listpermissions.php';</script>";
        exit();
    } else {
        // Hata durumunda kullanıcıya bildirim
        echo "Hata: " . $stmt->error;
    }

    $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yetki Güncelle</title>
    <?php require('../inc/links.php'); ?>
</head>
<body>
    <?php require('../inc/header.php'); ?>
    
    <!-- Yetki Güncelleme Başlık -->
    <div class="container d-flex justify-content-center align-items-center mt-5">
        <div class="w-50">
            <h3 class="text-center mb-4">Yetki Güncelle</h3>
            <!-- Form -->
            <form action="updatepermission.php?id=<?php echo $permission_id; ?>" method="post" class="row row-cols-lg-auto g-3 align-items-center">
                <div class="col-12">
                    <label for="yetkiAdi" class="col-form-label">Yetki Adı:</label>
                </div>
                <div class="col-12">
                    <input type="text" class="form-control" id="yetkiAdi" name="yetkiAdi" value="<?php echo $permission_name; ?>" placeholder="Yetki Adı" required>
                </div>
                
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Güncelle</button>
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
