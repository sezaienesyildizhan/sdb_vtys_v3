<?php
require('../dbcon.php'); // Veritabanı bağlantısı

$message = ""; // Hata veya başarı mesajı için değişken

// Silme işlemi
if (isset($_GET['delete'])) {
    $categoryID = intval($_GET['delete']);

    // Kategori silme sorgusu
    $deleteQuery = "DELETE FROM ProductCategory WHERE Category_ID = $categoryID";

    if (mysqli_query($connection, $deleteQuery)) {
        $message = "<div class='alert alert-success text-center'>Kategori başarıyla silindi!</div>";
    } else {
        $message = "<div class='alert alert-danger text-center'>Hata: " . mysqli_error($connection) . "</div>";
    }
}

// Kategorileri çekme sorgusu
$sqlquery = "SELECT * FROM ProductCategory";
$result = mysqli_query($connection, $sqlquery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ürün Kategorileri</title>
    <?php require('../inc/links.php'); ?> <!-- Bootstrap ve gerekli linkler -->
</head>
<body>
    <?php require('../inc/header.php'); ?> <!-- Header -->

    <div class="container mt-5">
        <!-- Sayfa Başlığı -->
        <h2 class="mb-4 text-center">Ürün Kategorileri</h2>

        <!-- Başarı/Hata Mesajı -->
        <?php if (!empty($message)) echo $message; ?>

        <!-- Kategori Tablosu -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Kategori ID</th>
                        <th>Kategori Adı</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>
                                    <td>{$row['Category_ID']}</td>
                                    <td>{$row['Category_Name']}</td>
                                    <td>
                                        <a href='updatecategory.php?id={$row['Category_ID']}' class='btn btn-warning btn-sm'>Düzenle</a>
                                        <a href='?delete={$row['Category_ID']}' class='btn btn-danger btn-sm' onclick=\"return confirm('Bu kategoriyi silmek istediğinizden emin misiniz?');\">Sil</a>
                                    </td>
                                </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' class='text-center'>Kategori bulunamadı.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Kategori Ekle Butonu -->
        <div class="text-center mt-4">
            <a href="addcategory.php" class="btn btn-primary btn-lg">Kategori Ekle</a>
        </div>
    </div>
</body>
</html>
