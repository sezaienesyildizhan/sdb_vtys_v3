<?php
require('../dbcon.php'); // Veritabanı bağlantısını dahil et

$message = ""; // Hata veya başarı mesajı
$categoryID = ""; // Kategori ID
$categoryName = ""; // Kategori Adı

// GET ile gelen ID'yi al ve kategori bilgilerini çek
if (isset($_GET['id'])) {
    $categoryID = intval($_GET['id']);
    $query = "SELECT * FROM ProductCategory WHERE Category_ID = $categoryID";
    $result = mysqli_query($connection, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $category = mysqli_fetch_assoc($result);
        $categoryName = $category['Category_Name'];
    } else {
        $message = "<div class='alert alert-danger text-center'>Kategori bulunamadı!</div>";
    }
}

// Form gönderildiyse güncelleme işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['categoryName'])) {
    $updatedCategoryName = mysqli_real_escape_string($connection, $_POST['categoryName']);

    if (!empty($updatedCategoryName)) {
        $updateQuery = "UPDATE ProductCategory SET Category_Name = '$updatedCategoryName' WHERE Category_ID = $categoryID";

        if (mysqli_query($connection, $updateQuery)) {
            // Başarı durumunda yönlendirme
            echo "<script>window.location.href='/db/adminpanel/products/productcategories.php';</script>";
            exit();
        } else {
            $message = "<div class='alert alert-danger text-center'>Güncelleme hatası: " . mysqli_error($connection) . "</div>";
        }
    } else {
        $message = "<div class='alert alert-warning text-center'>Kategori adı boş bırakılamaz!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori Güncelle</title>
    <?php require('../inc/links.php'); ?> <!-- Bootstrap ve gerekli linkler -->
</head>
<body>
    <?php require('../inc/header.php'); ?> <!-- Header -->

    <div class="container mt-5">
        <!-- Sayfa Başlığı -->
        <h2 class="mb-4 text-center">Kategori Güncelle</h2>

        <!-- Hata veya Başarı Mesajı -->
        <?php if (!empty($message)) echo $message; ?>

        <!-- Kategori Güncelleme Formu -->
        <form action="updatecategory.php?id=<?php echo $categoryID; ?>" method="POST">
            <div class="card">
                <div class="card-header bg-dark text-white text-center">
                    Kategori Bilgileri
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="categoryName" class="form-label">Kategori Adı</label>
                        <input type="text" class="form-control" id="categoryName" name="categoryName" 
                               value="<?php echo htmlspecialchars($categoryName); ?>" required>
                    </div>
                </div>
            </div>

            <!-- Form Butonları -->
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg">Güncelle</button>
                <a href="productcategories.php" class="btn btn-secondary btn-lg">İptal</a>
            </div>
        </form>
    </div>

</body>
</html>
