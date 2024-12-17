<?php
// dbcon.php dosyasını dahil et
require_once('../dbcon.php');

// Veritabanına bağlantı kontrolü
if ($connection->connect_error) {
    die("Veritabanı bağlantısı başarısız: " . $connection->connect_error);
}

// Eğer silme isteği varsa
if (isset($_GET['id'])) {
    $role_id = $_GET['id'];

    // Rolün herhangi bir yetkiye sahip olup olmadığını kontrol et
    $check_query = "SELECT COUNT(*) AS total FROM RoleAuthorizations WHERE Role_ID = ?";
    $check_stmt = $connection->prepare($check_query);
    $check_stmt->bind_param("i", $role_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result()->fetch_assoc();

    if ($check_result['total'] > 0) {
        // Rol yetkiliyse silmeye izin verme
        echo "<script>alert('Bu rol bir yetkiye sahip olduğu için silinemez.'); window.location.href='/db/adminpanel/roles/listroles.php';</script>";
        exit();
    }

    // Rolü sil
    $delete_query = "DELETE FROM Roles WHERE Role_ID = ?";
    $stmt = $connection->prepare($delete_query);
    $stmt->bind_param("i", $role_id);

    if ($stmt->execute()) {
        // Başarılı bir şekilde silindi
        echo "<script>window.location.href='/db/adminpanel/roles/listroles.php';</script>";
        exit();
    } else {
        // Hata durumunda
        die("Silme işlemi başarısız: " . $stmt->error);
    }
}


// Roller tablosundan verileri al
$sql = "SELECT Role_ID, Role_Name FROM Roles";
$result = $connection->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rol Yönetimi</title>
    <?php require('../inc/links.php'); ?>
</head>
<body>
    <?php require('../inc/header.php'); ?>

    <div class="container mt-5">
        <h3 class="text-start mb-3">Roller</h3>
        <div class="table-responsive">
            <table class="table table-striped table-hover text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Rol ID</th>
                        <th>Rol Adı</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Eğer veritabanından veri varsa, her satır için veri çek
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>" . $row['Role_ID'] . "</td>
                                    <td>" . $row['Role_Name'] . "</td>
                                    <td>
                                        <a href='updaterole.php?id=" . $row['Role_ID'] . "' class='btn btn-warning btn-sm'>Düzenle</a>
                                        <!-- Silme işlemi için link -->
                                        <a href='listroles.php?id=" . $row['Role_ID'] . "' class='btn btn-danger btn-sm'>Sil</a>
                                        <a href='roledetails.php?id=" . $row['Role_ID'] . "' class='btn btn-success btn-sm'>Detay</a>
                                    </td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3'>Veri bulunamadı</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- rol ekle butonu -->
        <a href="addrole.php" class="btn btn-primary">Rol Ekle</a>
        <br><br>
        <a href="listpermissions.php" class="btn btn-secondary">Yetkiler</a>
    </div>

</body>
</html>

<?php
// Veritabanı bağlantısını kapat
$connection->close();
?>
