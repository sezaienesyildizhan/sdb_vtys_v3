<?php
require('../dbcon.php'); // Veritabanı bağlantısını dahil et

// Hata veya başarı mesajı için değişken
$message = "";

// Form gönderilmişse veriyi kaydet
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Formdan gelen veriyi al ve temizle
    $categoryName = mysqli_real_escape_string($connection, $_POST['categoryName']);

    // Boş veri kontrolü
    if (!empty($categoryName)) {
        // Kategori ekleme sorgusu
        $sql = "INSERT INTO ProductCategory (Category_Name) VALUES ('$categoryName')";

        // Sorguyu çalıştır ve sonucu kontrol et
        if (mysqli_query($connection, $sql)) {
            $message = "<div class='alert alert-success text-center'>Kategori başarıyla eklendi!</div>";
        } else {
            $message = "<div class='alert alert-danger text-center'>Hata: " . mysqli_error($connection) . "</div>";
        }
    } else {
        $message = "<div class='alert alert-warning text-center'>Lütfen kategori adını girin.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Ekle</title>
    <?php require('../inc/links.php'); ?> <!-- Bootstrap ve gerekli linkler -->
</head>
<body>
    <?php require('../inc/header.php'); ?> <!-- Header -->

    <div class="container mt-5">
        <!-- Sayfa Başlığı -->
        <h2 class="mb-4 text-center">Kategori Ekle</h2>

        <!-- Hata veya Başarı Mesajı -->
        <?php if (!empty($message)) echo $message; ?>

        <!-- Kategori Ekleme Formu -->
        <form action="" method="POST">
            <div class="card">
                <div class="card-header bg-dark text-white text-center">
                    Yeni Kategori Bilgileri
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="categoryName" class="form-label">Kategori Adı</label>
                        <input type="text" class="form-control" id="categoryName" name="categoryName" placeholder="Kategori Adını Girin" required>
                    </div>
                </div>
            </div>

            <!-- Form Butonları -->
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg">Kaydet</button>
                <a href="productcategories.php" class="btn btn-secondary btn-lg">İptal</a>
            </div>
        </form>
    </div>
</body>
</html>
