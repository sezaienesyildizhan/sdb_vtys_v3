<?php


// Veritabanı bağlantısı
require_once('../dbcon.php'); // dbcon.php dosyasını dahil et

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    // Silinecek kullanıcının ID'sini al
    $user_id_to_delete = intval($_GET['id']); // GET parametresi ile ID'yi alıyoruz

    // Silme sorgusu
    $delete_query = "DELETE FROM Users WHERE User_ID = ?";
    $stmt = $connection->prepare($delete_query);

    if ($stmt === false) {
        die("Hazırlama hatası: " . $connection->error);
    }

    $stmt->bind_param("i", $user_id_to_delete);

    if ($stmt->execute()) {
        // Başarıyla silindiyse, kullanıcıyı silip user.php'ye yönlendir
        echo "<script>alert('Kullanıcı başarıyla silindi!'); window.location.href='../user.php';</script>";
    } else {
        // Hata durumunda hata mesajını göster
        echo "<script>alert('Kullanıcı silinemedi: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}



// Kullanıcı ID'sini URL'den al
if (isset($_GET['id'])) {
    $user_id = intval($_GET['id']);

    // Veritabanından kullanıcıyı çek
    $query = "SELECT u.User_ID, u.User_Name, u.User_Surname, u.User_Username, u.User_PhoneNumber, u.User_Email, u.User_RegisteredAt, r.Role_Name
              FROM Users u 
              JOIN Roles r ON u.Role_ID = r.Role_ID
              WHERE u.User_ID = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo "<script>alert('Kullanıcı bulunamadı!'); window.location.href='user.php';</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('Geçersiz kullanıcı ID!'); window.location.href='user.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Detayları</title>
    <?php require('../inc/links.php'); ?>
</head>
<body>

<?php require('../inc/header.php'); ?>
<!-- Header Alanı -->

<div class="container mt-5">
    <h3 class="mb-3">Kullanıcı Detayları</h3>
    
    <!-- Kullanıcı Detayları Tablosu -->
    <table class="table table-bordered">
        <tr>
            <th>Kullanıcı Adı</th>
            <td><?php echo $user['User_Name']; ?></td>
        </tr>
        <tr>
            <th>Soyad</th>
            <td><?php echo $user['User_Surname']; ?></td>
        </tr>
        <tr>
            <th>Kullanıcı Adı</th>
            <td><?php echo $user['User_Username']; ?></td>
        </tr>
        <tr>
            <th>Telefon Numarası</th>
            <td><?php echo $user['User_PhoneNumber']; ?></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?php echo $user['User_Email']; ?></td>
        </tr>
        <tr>
            <th>Kayıt Tarihi</th>
            <td><?php echo $user['User_RegisteredAt']; ?></td>
        </tr>
        <tr>
            <th>Rol</th>
            <td><?php echo $user['Role_Name']; ?></td>
        </tr>
    </table>

    <!-- Sil ve Güncelle Butonları -->
    <div class="row">
        <div class="col-12 d-flex ">
            <!-- Silme Butonu -->
            <form method="POST" action="userdetails.php?id=<?php echo $user['User_ID']; ?>" onsubmit="return confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?');" class="w-50 pr-2">
             <button type="submit" name="delete_user" class="btn btn-danger w-200">Kullanıcıyı Sil</button>
            </form>

            <!-- Güncelleme Butonu -->
            <a href="userupdate.php?id=<?php echo $user['User_ID']; ?>" class="btn btn-warning w-100">Kullanıcıyı Güncelle</a>

        </div>
    </div>

    <!-- Geri Dön Butonu -->
    <a href="../user.php" class="btn btn-secondary mt-3">Geri Dön</a>
</div>

</body>
</html>
