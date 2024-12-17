<?php
require('../dbcon.php'); // Veritabanı bağlantısını dahil et

// Hata mesajlarını saklamak için değişkenler
$message = "";

// Form gönderilmişse işlemleri gerçekleştir
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productName = mysqli_real_escape_string($connection, $_POST['productName']);
    $productDescription = mysqli_real_escape_string($connection, $_POST['productDescription']);
    $productPrice = mysqli_real_escape_string($connection, $_POST['productPrice']);
    $categoryID = mysqli_real_escape_string($connection, $_POST['categoryID']);

    // Boş alan kontrolü
    if (!empty($productName) && !empty($productPrice) && !empty($categoryID)) {
        // Ürün ekleme sorgusu
        $sql = "INSERT INTO Products (Category_ID, Product_Name, Product_Description, Product_Price)
                VALUES ('$categoryID', '$productName', '$productDescription', '$productPrice')";

        if (mysqli_query($connection, $sql)) {
            $message = "<div class='alert alert-success text-center'>Ürün başarıyla eklendi!</div>";
        } else {
            $message = "<div class='alert alert-danger text-center'>Hata: " . mysqli_error($connection) . "</div>";
        }
    } else {
        $message = "<div class='alert alert-warning text-center'>Lütfen tüm alanları doldurun.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ürün Ekle</title>
    <?php require('../inc/links.php'); ?>
</head>
<body>
    <!-- Navbar -->
    <?php require('../inc/header.php'); ?>

    <div class="container mt-5">
        <h2 class="text-center mb-4">Ürün Ekle</h2>

        <!-- Hata veya Başarı Mesajı -->
        <?php if (!empty($message)) echo $message; ?>

        <!-- Ürün Ekleme Formu -->
        <form action="addproduct.php" method="POST">
            <!-- Ürün Bilgileri -->
            <div class="card mb-3">
                <div class="card-header bg-dark text-white">
                    Ürün Bilgileri
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="productName" class="form-label">Ürün Adı</label>
                        <input type="text" class="form-control" id="productName" name="productName" placeholder="Ürün adını girin" required>
                    </div>
                    <div class="mb-3">
                        <label for="productDescription" class="form-label">Açıklama</label>
                        <textarea class="form-control" id="productDescription" name="productDescription" rows="3" placeholder="Ürün açıklamasını girin"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="productPrice" class="form-label">Fiyat</label>
                        <input type="number" step="0.01" class="form-control" id="productPrice" name="productPrice" placeholder="Ürün fiyatını girin" required>
                    </div>
                </div>
            </div>

            <!-- Kategori Seçimi -->
            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">
                    Kategori Seçimi
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="categoryID" class="form-label">Kategori</label>
                        <select class="form-select" id="categoryID" name="categoryID" required>
                            <option value="">Kategori Seçin</option>
                            <?php
                            // Kategorileri veritabanından çekme
                            $categoryQuery = "SELECT Category_ID, Category_Name FROM ProductCategory";
                            $categoryResult = mysqli_query($connection, $categoryQuery);

                            if ($categoryResult) {
                                while ($row = mysqli_fetch_assoc($categoryResult)) {
                                    echo "<option value='{$row['Category_ID']}'>{$row['Category_Name']}</option>";
                                }
                            } else {
                                echo "<option value=''>Kategori Bulunamadı</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Form Gönder Butonu -->
            <div class="text-center">
                <button type="submit" class="btn btn-success btn-lg">Ürün Ekle</button>
                <a href="../product.php" class="btn btn-secondary btn-lg">İptal</a>
            </div>
        </form>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
