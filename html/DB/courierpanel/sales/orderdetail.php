<?php
require('../dbcon.php'); // Veritabanı bağlantısı

// Sipariş ID'yi al
$sale_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($sale_id > 0) {
    // Sipariş Bilgileri Sorgusu
    $orderQuery = "
        SELECT Sales.*, 
               Users.User_Name AS Courier_Name, 
               SaleStatus.SaleStatu_Name, 
               DeliveryLocation.DeliveryLocation_Latitude, 
               DeliveryLocation.DeliveryLocation_Longitude
        FROM Sales
        JOIN Couriers ON Sales.Courier_ID = Couriers.Courier_ID
        JOIN Users ON Couriers.User_ID = Users.User_ID
        LEFT JOIN SaleStatus ON Sales.SaleStatu_ID = SaleStatus.SaleStatu_ID
        LEFT JOIN DeliveryLocation ON Sales.Sale_ID = DeliveryLocation.Sale_ID
        WHERE Sales.Sale_ID = $sale_id
    ";
    $orderResult = mysqli_query($connection, $orderQuery);
    $orderData = mysqli_fetch_assoc($orderResult);

    // Ödeme Bilgileri Sorgusu
    $paymentQuery = "
        SELECT Payment.Payment_Amount, 
               Payment.Payment_Time, 
               PaymentMethods.PaymentMethod_Name,
               PaymentStatus.PaymentStatus_Name
        FROM Payment
        JOIN PaymentMethods ON Payment.PaymentMethod_ID = PaymentMethods.PaymentMethod_ID
        LEFT JOIN PaymentStatus ON Payment.PaymentStatus_ID = PaymentStatus.PaymentStatus_ID
        WHERE Payment.Sale_ID = $sale_id
    ";
    $paymentResult = mysqli_query($connection, $paymentQuery);
    $paymentData = mysqli_fetch_assoc($paymentResult);

    // Ödeme tutarı için kontrol
    $paymentAmount = isset($paymentData['Payment_Amount']) && $paymentData['Payment_Amount'] > 0
                     ? number_format($paymentData['Payment_Amount'], 2)
                     : '0.00';

    // Ürün Bilgileri Sorgusu
    $productQuery = "
        SELECT SaleItems.*, 
               Products.Product_Name, 
               Products.Product_Price, 
               ProductCategory.Category_Name
        FROM SaleItems
        JOIN Products ON SaleItems.Product_ID = Products.Product_ID
        JOIN ProductCategory ON Products.Category_ID = ProductCategory.Category_ID
        WHERE SaleItems.Sale_ID = $sale_id
    ";
    $productResult = mysqli_query($connection, $productQuery);
} else {
    die("Sipariş ID bulunamadı.");
}

$latitude = htmlspecialchars($orderData['DeliveryLocation_Latitude'] ?? '');
$longitude = htmlspecialchars($orderData['DeliveryLocation_Longitude'] ?? '');

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipariş Detayı</title>
    <?php require('../inc/links.php'); ?>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD7u8MJye2yoWklFvAgXFUFrS_ZO4pNjFo&libraries=places"></script>
</head>
<body>
    <?php require('../inc/header2.php'); ?>

    <div class="container mt-5">
        <h2 class="mb-4 text-center">Sipariş Detayı</h2>

        <!-- Sipariş Bilgileri -->
        <div class="card mb-4">
            <div class="card-header bg-dark text-white">Sipariş Bilgileri</div>
            <div class="card-body">
                <p><strong>Sipariş ID:</strong> <?php echo htmlspecialchars($orderData['Sale_ID'] ?? ''); ?></p>
                <p><strong>Kurye Adı:</strong> <?php echo htmlspecialchars($orderData['Courier_Name'] ?? 'Bilinmiyor'); ?></p>
                <p><strong>Sipariş Durumu:</strong> <?php echo htmlspecialchars($orderData['SaleStatu_Name'] ?? 'Bilinmiyor'); ?></p>
                <p><strong>Atanma Tarihi:</strong> <?php echo htmlspecialchars($orderData['Sale_AssignedDate'] ?? ''); ?></p>
                <p><strong>Güncelleme Zamanı:</strong> <?php echo htmlspecialchars($orderData['Sale_UpdatedAt'] ?? ''); ?></p>
                <p><strong>Toplam Tutar:</strong> <?php echo number_format($orderData['Sale_TotalPrice'] ?? 0, 2); ?> ₺</p>
                <p><strong>Teslimat Konumu:</strong> 
                    Enlem: <?php echo htmlspecialchars($orderData['DeliveryLocation_Latitude'] ?? ''); ?>, 
                    Boylam: <?php echo htmlspecialchars($orderData['DeliveryLocation_Longitude'] ?? ''); ?>
                </p>
            </div>
        </div>

        <!-- Harita Bilgileri -->
        <div class="card mb-4">
                <div class="card-header bg-secondary text-white">Teslimat Adresi</div>
                <div class="card-body">
                    <div id="map" style="height: 400px; margin-bottom: 20px;"></div>
                </div>
        </div>

        <!-- Ödeme Bilgileri -->
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">Ödeme Bilgileri</div>
            <div class="card-body">
                <p><strong>Ödeme Yöntemi:</strong> <?php echo htmlspecialchars($paymentData['PaymentMethod_Name'] ?? 'Bilinmiyor'); ?></p>
                <p><strong>Ödeme Durumu:</strong> 
                    <?php echo htmlspecialchars($paymentData['PaymentStatus_Name'] ?? 'Bilinmiyor'); ?>
                </p>
                <p><strong>Ödeme Zamanı:</strong> <?php echo htmlspecialchars($paymentData['Payment_Time'] ?? ''); ?></p>
                <p><strong>Ödeme Tutarı:</strong> <?php echo $paymentAmount; ?> ₺</p>
            </div>
        </div>

        <!-- Sipariş Ürünleri -->
        <div class="card">
            <div class="card-header bg-secondary text-white">Sipariş Ürünleri</div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 text-center align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>Ürün ID</th>
                            <th>Ürün Adı</th>
                            <th>Kategori</th>
                            <th>Miktar</th>
                            <th>Birim Fiyat</th>
                            <th>Toplam Fiyat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($product = mysqli_fetch_assoc($productResult)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($product['Product_ID'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($product['Product_Name'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($product['Category_Name'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($product['Quantity'] ?? 0); ?></td>
                                <td><?php echo number_format($product['Product_Price'] ?? 0, 2); ?> ₺</td>
                                <td><?php echo number_format(($product['Quantity'] ?? 0) * ($product['Product_Price'] ?? 0), 2); ?> ₺</td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function initMap() {
            // PHP'den gelen enlem ve boylam değerleri
            const latitude = <?php echo json_encode($latitude); ?>;
            const longitude = <?php echo json_encode($longitude); ?>;

            // Harita merkezini belirle
            const location = { lat: parseFloat(latitude), lng: parseFloat(longitude) };

            // Haritayı oluştur
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 15, // Yakınlaştırma seviyesi
                center: location, // Harita merkezi
            });

            // İşaretçi ekle
            new google.maps.Marker({
                position: location,
                map: map,
                title: "Teslimat Adresi", // İşaretçi başlığı
            });
        }
        window.onload = initMap;
    </script>

</body>
</html>

<?php echo htmlspecialchars($orderData['DeliveryLocation_Latitude'] ?? ''); ?>
<?php echo htmlspecialchars($orderData['DeliveryLocation_Longitude'] ?? ''); ?>