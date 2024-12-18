<?php
require('../dbcon.php'); // Veritabanı bağlantısını dahil et

// Hata mesajı ve form verileri için değişkenler
$message = "";
$product = null;

// Ürün ID'sini GET ile al ve kontrol et
if (isset($_GET['id'])) {
    $productID = intval($_GET['id']);

    // Ürün bilgilerini veritabanından çek
    $query = "SELECT * FROM Products WHERE Product_ID = $productID";
    $result = mysqli_query($connection, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);
    } else {
        $message = "<div class='alert alert-danger text-center'>Ürün bulunamadı!</div>";
    }
} else {
    $message = "<div class='alert alert-danger text-center'>Geçersiz ürün ID!</div>";
}

// Form gönderildiğinde güncelleme işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $productName = mysqli_real_escape_string($connection, $_POST['productName']);
    $productDescription = mysqli_real_escape_string($connection, $_POST['productDescription']);
    $productPrice = mysqli_real_escape_string($connection, $_POST['productPrice']);
    $categoryID = intval($_POST['categoryID']);

    if (!empty($productName) && !empty($productPrice) && !empty($categoryID)) {
        // Ürün güncelleme sorgusu
        $updateQuery = "UPDATE Products 
                        SET Product_Name = '$productName', 
                            Product_Description = '$productDescription', 
                            Product_Price = '$productPrice', 
                            Category_ID = '$categoryID' 
                        WHERE Product_ID = $productID";

        if (mysqli_query($connection, $updateQuery)) {
            // Başarı durumunda direkt yönlendirme
            echo "<script>window.location.href='/db/adminpanel/product.php';</script>";
            exit();
        } else {
            $message = "<div class='alert alert-danger text-center'>Güncelleme hatası: " . mysqli_error($connection) . "</div>";
        }
    } else {
        $message = "<div class='alert alert-warning text-center'>Lütfen tüm alanları doldurun!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ürün Güncelle</title>
    <?php require('../inc/links.php'); ?>
</head>
<body>
    <!-- Navbar -->
    <?php require('../inc/header.php'); ?>

    <div class="container mt-5">
        <h2 class="text-center mb-4">Ürün Güncelle</h2>

        <!-- Hata veya Başarı Mesajı -->
        <?php if (!empty($message)) echo $message; ?>

        <?php if ($product): ?>
        <!-- Ürün Güncelleme Formu -->
        <form action="updateproduct.php?id=<?php echo $productID; ?>" method="POST">
            <!-- Ürün Bilgileri -->
            <div class="card mb-3">
                <div class="card-header bg-dark text-white">Ürün Bilgileri</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="productName" class="form-label">Ürün Adı</label>
                        <input type="text" class="form-control" id="productName" name="productName" 
                               value="<?php echo htmlspecialchars($product['Product_Name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="productDescription" class="form-label">Açıklama</label>
                        <textarea class="form-control" id="productDescription" name="productDescription" rows="3"><?php echo htmlspecialchars($product['Product_Description']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="productPrice" class="form-label">Fiyat</label>
                        <input type="number" step="0.01" class="form-control" id="productPrice" name="productPrice" 
                               value="<?php echo htmlspecialchars($product['Product_Price']); ?>" required>
                    </div>
                </div>
            </div>

            <!-- Kategori Seçimi -->
            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">Kategori Seçimi</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="categoryID" class="form-label">Kategori</label>
                        <select class="form-select" id="categoryID" name="categoryID" required>
                            <option value="">Kategori Seçin</option>
                            <?php
                            // Kategorileri çekme
                            $categoryQuery = "SELECT Category_ID, Category_Name FROM ProductCategory";
                            $categoryResult = mysqli_query($connection, $categoryQuery);

                            if ($categoryResult) {
                                while ($row = mysqli_fetch_assoc($categoryResult)) {
                                    $selected = ($row['Category_ID'] == $product['Category_ID']) ? "selected" : "";
                                    echo "<option value='{$row['Category_ID']}' $selected>{$row['Category_Name']}</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Form Gönder Butonu -->
            <div class="text-center">
                <button type="submit" class="btn btn-success btn-lg">Güncelle</button>
                <a href="../product.php" class="btn btn-secondary btn-lg">İptal</a>
            </div>
        </form>
        <?php endif; ?>
    </div>
</body>
</html>
