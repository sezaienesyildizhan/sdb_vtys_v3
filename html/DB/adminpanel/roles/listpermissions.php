<?php

// listpermissions.php dosyasında güncelleme:
require_once('../dbcon.php'); // dbcon.php dosyasını dahil et

// Silme işlemi: Yetkiyi silemeden önce kontrol et
if (isset($_GET['id'])) {
    $permission_id = $_GET['id'];

    // Yetkinin bir rolde tanımlı olup olmadığını kontrol et
    $check_query = "SELECT COUNT(*) AS total FROM RoleAuthorizations WHERE Permission_ID = ?";
    $check_stmt = $connection->prepare($check_query);
    $check_stmt->bind_param("i", $permission_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result()->fetch_assoc();

    if ($check_result['total'] > 0) {
        // Yetki bir rolde tanımlıysa silmeye izin verme
        echo "<script>alert('Bu yetki bir rolde tanımlı olduğu için silinemez.'); window.location.href='/db/adminpanel/roles/listpermissions.php';</script>";
        exit();
    }

    // Yetkiyi sil
    $delete_query = "DELETE FROM RolePermissions WHERE Permission_ID = ?";
    $stmt = $connection->prepare($delete_query);
    $stmt->bind_param("i", $permission_id);

    if ($stmt->execute()) {
        // Başarılı bir şekilde silindi
        echo "<script>window.location.href='/db/adminpanel/roles/listpermissions.php';</script>";
        exit();
    } else {
        // Hata durumunda
        die("Silme işlemi başarısız: " . $stmt->error);
    }
}

// Veritabanından RolePermissions tablosundaki verileri çek
$query = "SELECT Permission_ID, Permission_Name FROM RolePermissions";
$result = $connection->query($query);

if (!$result) {
    die("Veritabanı sorgusu başarısız: " . $connection->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yetkiler</title>
    <?php require('../inc/links.php'); ?>
</head>
<body>
    <?php require('../inc/header.php'); ?>
    <!-- Header Alanı -->

    <div class="container mt-5">
        <!-- Bootstrap Table -->
        <h3 class="text-start mb-3">Yetkiler</h3>
        <div class="table-responsive">
            <table class="table table-striped table-hover text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Yetki ID</th>
                        <th>Yetki Adı</th>
                        <th>Yetki İşlemleri</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $row['Permission_ID']; ?></td>
                            <td><?php echo $row['Permission_Name']; ?></td>
                            <td>
                                <a href="updatepermission.php?id=<?php echo $row['Permission_ID']; ?>" class="btn btn-warning btn-sm">Düzenle</a>
                                <!-- Silme işlemi için link -->
                                <a href="?id=<?php echo $row['Permission_ID']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bu yetkiyi silmek istediğinizden emin misiniz?')">Sil</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Yetki ekleme butonu -->
        <br><br>
        <a href="addpermission.php" class="btn btn-secondary">Yetki Ekle</a>
    </div>
</body>
</html>
