<?php

// Veritabanı bağlantısı
require_once('../dbcon.php');

// Role_ID'yi URL'den al
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('Role_ID bulunamadı!'); window.location.href='listroles.php';</script>";
    exit;
}

$role_id = intval($_GET['id']); // Güvenlik için int dönüşümü

// Eğer form gönderildiyse
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Formdan gelen yetki (permissions) verisini al
    $permissions = isset($_POST['permissions']) ? $_POST['permissions'] : [];

    // Veritabanı işlemlerini başlat
    $connection->begin_transaction();

    try {
        // Mevcut yetkileri sil
        $delete_sql = "DELETE FROM RoleAuthorizations WHERE Role_ID = ?";
        $stmt = $connection->prepare($delete_sql);
        $stmt->bind_param("i", $role_id);
        $stmt->execute();
        $stmt->close();

        // Yeni yetkileri ekle
        if (!empty($permissions)) {
            $insert_sql = "INSERT INTO RoleAuthorizations (Role_ID, Permission_ID) VALUES (?, ?)";
            $stmt = $connection->prepare($insert_sql);

            foreach ($permissions as $permission_id) {
                $permission_id = intval($permission_id); // Güvenlik için int dönüşümü
                $stmt->bind_param("ii", $role_id, $permission_id);
                $stmt->execute();
            }

            $stmt->close();
        }

        // İşlemleri onayla
        $connection->commit();

        // Başarılı mesajı ve yönlendirme
        echo "<script>alert('Rolün yetkileri başarıyla güncellendi!'); window.location.href='listroles.php';</script>";
        exit;
    } catch (Exception $e) {
        // Hata durumunda işlemleri geri al
        $connection->rollback();

        // Hata mesajı
        echo "<script>alert('Hata: Yetkiler güncellenirken bir sorun oluştu!');</script>";
    }
}

// RolePermissions tablosundan yetki listesi al
$permissions_sql = "SELECT Permission_ID, Permission_Name FROM RolePermissions";
$permissions_result = $connection->query($permissions_sql);
$permissions_list = [];
if ($permissions_result->num_rows > 0) {
    while ($row = $permissions_result->fetch_assoc()) {
        $permissions_list[] = $row;
    }
}

// RoleAuthorizations tablosundan mevcut yetkileri al
$current_permissions_sql = "SELECT Permission_ID FROM RoleAuthorizations WHERE Role_ID = ?";
$stmt = $connection->prepare($current_permissions_sql);
$stmt->bind_param("i", $role_id);
$stmt->execute();
$result = $stmt->get_result();
$current_permissions = [];
while ($row = $result->fetch_assoc()) {
    $current_permissions[] = $row['Permission_ID'];
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rol Yetki Güncelleme</title>
    <!-- Bootstrap CSS -->
    <?php require('../inc/links.php'); ?>
</head>
<body>
    <?php require('../inc/header.php'); ?>

    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Rol Yetkilerini Güncelle</h2>
            <a href="listroles.php" class="btn btn-secondary">Geri Dön</a>
        </div>

        <form method="post">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">Yetkiler</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($permissions_list)) { ?>
                        <?php foreach ($permissions_list as $permission) { ?>
                            <div class="form-check mb-2">
                                <input 
                                    class="form-check-input" 
                                    type="checkbox" 
                                    name="permissions[]" 
                                    value="<?php echo $permission['Permission_ID']; ?>" 
                                    id="perm_<?php echo $permission['Permission_ID']; ?>"
                                    <?php echo in_array($permission['Permission_ID'], $current_permissions) ? 'checked' : ''; ?>
                                >
                                <label class="form-check-label" for="perm_<?php echo $permission['Permission_ID']; ?>">
                                    <?php echo htmlspecialchars($permission['Permission_Name']); ?>
                                </label>
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <p class="text-muted">Henüz bir yetki tanımlanmamış.</p>
                    <?php } ?>
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-end gap-2">
                <button type="submit" class="btn btn-success">Kaydet</button>
                <a href="listroles.php" class="btn btn-danger">İptal</a>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Veritabanı bağlantısını kapat
$connection->close();
?>
