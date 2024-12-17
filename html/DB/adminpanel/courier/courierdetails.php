<?php
require('../dbcon.php'); // Veritabanı bağlantı dosyası
mysqli_set_charset($connection, "utf8mb4"); // Karakter seti

// ID parametresini al ve doğrula
if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Geçersiz ID");
}
$courier_id = intval($_GET['id']);

// Kurye detaylarını getir
$query = "
    SELECT 
        c.Courier_ID, 
        u.User_Name AS Ad, 
        u.User_Surname AS Soyad, 
        u.User_Username AS KullaniciAdi, 
        u.User_PhoneNumber AS Telefon, 
        c.Courier_IsActive AS Aktif, 
        al.Action_Note AS GorevDurumu 
    FROM Couriers c
    JOIN Users u ON c.User_ID = u.User_ID
    LEFT JOIN ActionLog al ON c.Courier_ID = al.Courier_ID
    WHERE c.Courier_ID = $courier_id
";
$result = mysqli_query($connection, $query);

if (!$result || mysqli_num_rows($result) == 0) {
    die("Kurye bulunamadı.");
}

$courier = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kurye Detayları</title>
    <?php require('../inc/links.php'); ?> <!-- Yol güncellendi -->
</head>
<body>
    <?php require('../inc/header.php'); ?> <!-- Yol güncellendi -->
    <div class="container mt-5">
        <h3 class="mb-3">Kurye Detayları</h3>
        <table class="table table-bordered">
            <tr>
                <th>Kurye ID</th>
                <td><?php echo $courier['Courier_ID']; ?></td>
            </tr>
            <tr>
                <th>Ad</th>
                <td><?php echo $courier['Ad']; ?></td>
            </tr>
            <tr>
                <th>Soyad</th>
                <td><?php echo $courier['Soyad']; ?></td>
            </tr>
            <tr>
                <th>Kullanıcı Adı</th>
                <td><?php echo $courier['KullaniciAdi']; ?></td>
            </tr>
            <tr>
                <th>Telefon</th>
                <td><?php echo $courier['Telefon']; ?></td>
            </tr>
            <tr>
                <th>Aktif/Pasif</th>
                <td><?php echo ($courier['Aktif'] == 1) ? 'Aktif' : 'Pasif'; ?></td>
            </tr>
            <tr>
                <th>Görev Durumu</th>
                <td><?php echo $courier['GorevDurumu'] ?? 'Görev Yok'; ?></td>
            </tr>
        </table>

        <a href="../courier.php" class="btn btn-primary">Geri Dön</a> <!-- Geri dön bağlantısı düzenlendi -->
    </div>
</body>
</html>

<?php
// Veritabanı bağlantısını kapat
mysqli_close($connection);
?>
