<?php
include("../adminpanel/dbcon.php"); // Veritabanı bağlantısı

// Stored Procedure: Kurye Satış Performansı
$query = "CALL GetCourierSalesPerformance()";
$courierResult = mysqli_query($connection, $query);
$courierData = [];
if ($courierResult) {
    while ($row = mysqli_fetch_assoc($courierResult)) {
        $courierData[] = $row; // Sonuçları diziye kaydet
    }
    mysqli_free_result($courierResult);
}
while (mysqli_more_results($connection)) { mysqli_next_result($connection); } // Bağlantıyı temizle

// Stored Procedure: Kategori Satış Sayısı
$saleNumberCategories = "CALL GetCategorySales()";
$saleNumberCategoriesResult = mysqli_query($connection, $saleNumberCategories);
$categoryData = [];
if ($saleNumberCategoriesResult) {
    while ($row = mysqli_fetch_assoc($saleNumberCategoriesResult)) {
        $categoryData[] = $row; // Sonuçları diziye kaydet
    }
    mysqli_free_result($saleNumberCategoriesResult);
}
while (mysqli_more_results($connection)) { mysqli_next_result($connection); } // Bağlantıyı temizle
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Satış Performansı</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <?php require("inc/links.php"); ?>
</head>
<body>
    <!-- Header -->
    <?php require("inc/header.php"); ?>

    <!-- Kurye Satış Performansı -->
    <div class="container mt-5">
        <h2 class="text-center mb-4">Kurye Satış Performansı</h2>
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Kurye Adı</th>
                    <th>Kurye Soyadı</th>
                    <th>Toplam Satış Tutarı</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if (!empty($courierData)) {
                    $count = 1;
                    foreach ($courierData as $row) {
                        echo "<tr>";
                        echo "<td>" . $count++ . "</td>";
                        echo "<td>" . htmlspecialchars($row['User_Name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['User_Surname']) . "</td>";
                        echo "<td>" . number_format($row['TotalSalesAmount'], 2) . " TL</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' class='text-center'>Veri bulunamadı veya hata oluştu.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Kategori Satış Raporu -->
    <div class="container mt-5">
        <h2 class="text-center mb-4">Kategori Satış Raporu</h2>
        <table class="table table-striped table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Kategori Adı</th>
                    <th>Toplam Satış Miktarı</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if (!empty($categoryData)) {
                    $count = 1;
                    foreach ($categoryData as $row) {
                        echo "<tr>";
                        echo "<td>" . $count++ . "</td>";
                        echo "<td>" . htmlspecialchars($row['Category_Name']) . "</td>";
                        echo "<td>" . number_format($row['TotalSoldQuantity']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3' class='text-center'>Veri bulunamadı veya hata oluştu.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Bootstrap JS ve jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
// Veritabanı bağlantısını kapat
mysqli_close($connection);
?>
