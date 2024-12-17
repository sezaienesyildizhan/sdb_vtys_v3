<?php
require('dbcon.php'); // Veritabanı bağlantısı

if (isset($_GET['delete'])) {
    $productID = (int)$_GET['delete'];

    // İlişkili kayıtları kontrol et
    $checkQuery = "SELECT COUNT(*) AS total FROM SaleItems WHERE Product_ID = ?";
    $stmt = $connection->prepare($checkQuery);
    $stmt->bind_param('i', $productID);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['total'] > 0) {
        // İlişkili kayıtlar bulundu, silme işlemini iptal et
        $message = "<div class='alert alert-danger text-center'>
                        Bu ürün silinemez! Ürün, mevcut satışlarda kullanılıyor.
                    </div>";
    } else {
        // İlişkili kayıt yok, ürünü sil
        $deleteQuery = "DELETE FROM Products WHERE Product_ID = ?";
        $stmt = $connection->prepare($deleteQuery);
        $stmt->bind_param('i', $productID);
        if ($stmt->execute()) {
            $message = "<div class='alert alert-success text-center'>Ürün başarıyla silindi!</div>";
        } else {
            $message = "<div class='alert alert-danger text-center'>Hata: " . $stmt->error . "</div>";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ürünler Yönetimi</title>
    <?php require('inc/links.php'); ?>
</head>
<body>
    <!-- Navbar -->
    <?php require('inc/header.php'); ?>

    <div class="container mt-4">
        <h2 class="text-center mb-4">Ürünler Yönetimi</h2>

        <!-- Silme veya Diğer Mesaj -->
        <?php if (isset($message)) echo $message; ?>

        <!-- Arama Kutusu -->
        <div class="row mb-3">
            <div class="col-md-6">
                <input type="text" id="search" class="form-control" placeholder="Ürün Adına Göre Ara...">
            </div>
            <div class="col-md-6 text-end">
                <a href="products/addproduct.php" class="btn btn-primary">Ürün Ekle</a>
                <a href="products/productcategories.php" class="btn btn-secondary">Kategoriler</a>
            </div>
        </div>

        <!-- Ürünler Tablosu -->
        <div class="table-responsive">
            <table class="table table-striped table-hover text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Ürün ID</th>
                        <th>Kategori</th>
                        <th>Ürün Adı</th>
                        <th>Açıklama</th>
                        <th>Fiyat</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody id="productTable">
                <?php
                    // SQL sorgusu: Ürünler ve kategori adlarını join ile getir
                    $sqlquery = "
                        SELECT p.Product_ID, p.Product_Name, p.Product_Description, p.Product_Price, 
                            c.Category_Name 
                        FROM Products p
                        LEFT JOIN ProductCategory c ON p.Category_ID = c.Category_ID
                    ";

                    $result = mysqli_query($connection, $sqlquery);
                    if (!$result) {
                        echo "<tr><td colspan='6'>Hata: " . mysqli_error($connection) . "</td></tr>";
                    } else {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>
                                    <td>{$row['Product_ID']}</td>
                                    <td>{$row['Category_Name']}</td>
                                    <td>{$row['Product_Name']}</td>
                                    <td>{$row['Product_Description']}</td>
                                    <td>{$row['Product_Price']} ₺</td>
                                    <td>
                                        <a href='products/updateproduct.php?id={$row['Product_ID']}' class='btn btn-warning btn-sm'>Düzenle</a>
                                        <a href='?delete={$row['Product_ID']}' class='btn btn-danger btn-sm' onclick=\"return confirm('Bu ürünü silmek istediğinizden emin misiniz?');\">Sil</a>
                                    </td>
                                </tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- JS Arama Özelliği -->
    <script>
        document.getElementById('search').addEventListener('input', function () {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('#productTable tr');
            rows.forEach(row => {
                const productName = row.cells[2].innerText.toLowerCase(); // Ürün Adı sütunu
                row.style.display = productName.includes(searchValue) ? '' : 'none';
            });
        });
    </script>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
