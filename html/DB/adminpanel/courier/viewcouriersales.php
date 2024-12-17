<?php
require('../dbcon.php'); // Veritabanı bağlantı dosyası
mysqli_set_charset($connection, "utf8mb4"); // Karakter seti

// ID parametresini al ve doğrula
if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Geçersiz Kurye ID");
}
$courier_id = intval($_GET['id']);

// Kurye satışlarını getir
$query = "
    SELECT 
        s.Sale_ID,
        s.Sale_TotalPrice AS ToplamFiyat,
        s.Sale_AssignedDate AS AtamaTarihi,
        s.Sale_UpdatedAt AS GüncellenmeTarihi,
        ss.SaleStatu_Name AS Durum
    FROM Sales s
    JOIN SaleStatus ss ON s.SaleStatu_ID = ss.SaleStatu_ID
    WHERE s.Courier_ID = $courier_id
";
$result = mysqli_query($connection, $query);

if (!$result) {
    die("Sorgu başarısız: " . mysqli_error($connection));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kurye Satışları</title>
    <?php require('../inc/links.php'); ?>
</head>
<body>
    <?php require('../inc/header.php'); ?>
    <div class="container mt-5">
        <h3 class="mb-3">Kurye Satışları</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Satış ID</th>
                    <th>Toplam Fiyat</th>
                    <th>Atama Tarihi</th>
                    <th>Güncellenme Tarihi</th>
                    <th>Durum</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $row['Sale_ID']; ?></td>
                            <td><?php echo number_format($row['ToplamFiyat'], 2); ?> TL</td>
                            <td><?php echo $row['AtamaTarihi']; ?></td>
                            <td><?php echo $row['GüncellenmeTarihi']; ?></td>
                            <td><?php echo $row['Durum']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5">Bu kurye ile ilişkili satış bulunamadı.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="../courier.php" class="btn btn-primary">Geri Dön</a>
    </div>
</body>
</html>

<?php
// Veritabanı bağlantısını kapat
mysqli_close($connection);
?>
