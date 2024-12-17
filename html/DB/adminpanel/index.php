<?php
// Dashbord queries
include ("../adminpanel/dbcon.php");

// 1. Toplam Kurye Sayısı
$totalCourierQuery = "CALL GetTotalCourierCount();";
$totalCourierResult = mysqli_query($connection, $totalCourierQuery);
$totalCourier = 0;

if ($totalCourierResult) {
    // Sütun adını 'ToplamKurye' olarak kullanıyoruz
    while ($row = mysqli_fetch_assoc($totalCourierResult)) {
        $totalCourier = $row['ToplamKurye'];
    }
    mysqli_free_result($totalCourierResult); // Sonuç kümesini serbest bırak
}
// Bağlantıyı temizle
while (mysqli_more_results($connection)) {
    mysqli_next_result($connection);
}


// 3. Teslim Edilen Sipariş Sayısı
$deliveredOrdersQuery = "CALL GetDeliveredOrdersCount();";
$deliveredOrdersResult = mysqli_query($connection, $deliveredOrdersQuery);

if (!$deliveredOrdersResult) {
    die("Sorgu hatası: " . mysqli_error($connection));
}

$deliveredOrders = 0; 
if ($deliveredOrdersResult) {
    while ($row = mysqli_fetch_assoc($deliveredOrdersResult)) {
        $deliveredOrders = $row['TeslimEdilen'];
    }
    mysqli_free_result($deliveredOrdersResult);
}

// Bağlantıyı temizle
while (mysqli_more_results($connection)) {
    mysqli_next_result($connection);
}

// 4. Dağıtımda Olan Kurye Sayısı // inDelivery
$inDeliveryQuery = "CALL GetCouriersInDeliveryCount();";
$inDeliveryResult = mysqli_query($connection, $inDeliveryQuery);
$inDelivery = 0; // Başlangıç değeri

if ($inDeliveryResult) {
    while ($row = mysqli_fetch_assoc($inDeliveryResult)) {
        // 'DagitimdaOlan' sütunundan değeri alıyoruz
        $inDelivery = $row['DagitimdaOlan'];
    }
    mysqli_free_result($inDeliveryResult); // Sonuç kümesini serbest bırak
}

// Bağlantıyı temizle
while (mysqli_more_results($connection)) {
    mysqli_next_result($connection);
}

// 5. En Çok Satış Yapan Kurye ismi ve satış sayısı // mostSalesCourier
$mostSalesCourierQuery = "CALL GetTopSalesCourier();";
$mostSalesCourierResult = mysqli_query($connection, $mostSalesCourierQuery);
$mostSalesCourierName = "";
$mostSalesCourierCount = 0;
if ($mostSalesCourierResult) {
    while ($row = mysqli_fetch_assoc($mostSalesCourierResult)) {
        $mostSalesCourierName = $row['KuryeAdi'];
        $mostSalesCourierCount = $row['ToplamSatis'];
    }
    mysqli_free_result($mostSalesCourierResult);
}
while (mysqli_more_results($connection)) {
    mysqli_next_result($connection);
}

// 6. En çok gelir getiren sipariş

$mostValuedSalesQuery = "CALL GetMaxSaleTotalPrice();";
$mostValuedSalesResult = mysqli_query($connection, $mostValuedSalesQuery);
$mostValuedSalePrice = 0;
if ($mostValuedSalesResult) {
    while ($row = mysqli_fetch_assoc($mostValuedSalesResult)) {
        $mostValuedSalePrice = $row['EnYuksekTutar'];
    }
    mysqli_free_result($mostValuedSalesResult);
}

while (mysqli_more_results($connection)) {
    mysqli_next_result($connection);
}






// 7. Toplam sipariş

$totalOrdersQuery = "CALL GetTotalSalesCount();";
$totalOrdersResult = mysqli_query($connection, $totalOrdersQuery);
$totalOrdersCount = 0;
if ($totalOrdersResult) {
    while ($row = mysqli_fetch_assoc($totalOrdersResult)) {
        $totalOrdersCount = $row['ToplamSatis'];
    }
    mysqli_free_result($totalOrdersResult);
}
while (mysqli_more_results($connection)) {
    mysqli_next_result($connection);
}





// 8. İptal Olan sipariş




// 9. En çok satılan ürün

$mostOrderedProductQuery = "CALL GetTopSellingProduct();";
$mostOrderedProductResult = mysqli_query($connection, $mostOrderedProductQuery);
$mostOrderedProductName = "";
$mostOrderedProductCount = 0;
if ($mostOrderedProductResult) {
    while ($row = mysqli_fetch_assoc($mostOrderedProductResult)) {
        $mostOrderedProductName = $row["UrunAdi"];
        $mostOrderedProductCount = $row["ToplamSatisMiktari"];
    }
    mysqli_free_result($mostOrderedProductResult);
}
while (mysqli_more_results($connection)) {
    mysqli_next_result($connection);
}





?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Bootstrap CSS -->
    <?php require('inc/links.php'); ?>
    <style>
        .card {
            border: 2px solid #ddd;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            transition: transform 0.2s ease;
        }
        .card:hover {
            transform: scale(1.03);
        }
        .dashboard-title {
            margin-bottom: 20px;
            font-size: 2rem;
            font-weight: bold;
        }
        .card-header {
            font-weight: bold;
            font-size: 1rem;
        }
        .card-icon {
            font-size: 1.5rem;
        }
    </style>
</head>
<body>
    <!-- header navbar -->
    <?php require('inc/header.php'); ?>
    <!-- Dashboard Container -->
    <div class="container mt-5">
        <h2 class="text-center dashboard-title">Dashboard</h2>

        <div class="row g-4">
            <!-- Total Courier -->
            <div class="col-md-3">
                <div class="card text-center border-primary">
                    <div class="card-header bg-primary text-white">
                        Toplam Kurye <i class="bi bi-file-earmark"></i>
                    </div>
                    <div class="card-body">
                        <h1 class="card-title">
                            <?php echo $totalCourier; ?>
                        </h1>
                    </div>
                </div>
            </div>

            <!-- En çok satış yapan kurye, ismi ve satış sayısı -->
            <div class="col-md-3">
                <div class="card text-center border-warning">
                    <div class="card-header bg-warning text-white">
                        En Çok Satış Yapan Kurye <i class="bi bi-file-earmark"></i>
                    </div>
                    <div class="card-body">
                        <!-- kurye ismi -->
                        <h3 class="card-title">
                            <?php echo $mostSalesCourierName; ?>
                        </h3>
                        <!-- satış sayısı -->
                        <h3 class="card-text">
                            <?php echo $mostSalesCourierCount; ?>
                        </h3>
                    </div>
                </div>
            </div>


            

            <!-- Arrived at Destination -->
            <div class="col-md-3">
                <div class="card text-center border-info">
                    <div class="card-header bg-info text-white">
                        Hedefe Ulaşan Toplam Kurye <i class="bi bi-layers"></i>
                    </div>
                    <div class="card-body">
                        <h1 class="card-title">
                           <?php echo $deliveredOrders; ?>
                        </h1>
                    </div>
                </div>
            </div>

            <!-- Out for Delivery -->
            <div class="col-md-3">
                <div class="card text-center border-success">
                    <div class="card-header bg-success text-white">
                        Dağıtımda olan toplam kurye <i class="bi bi-bicycle"></i>
                    </div>
                    <div class="card-body">
                        <h1 class="card-title">
                            <?php echo $inDelivery; ?>
                        </h1>
                    </div>
                </div>
            </div>

            <!-- Total Delivered -->
            <div class="col-md-3">
                <div class="card text-center border-success">
                    <div class="card-header bg-success text-white">
                        Teslim Edilen Sipariş <i class="bi bi-bar-chart"></i>
                    </div>
                    <div class="card-body">
                        <h1 class="card-title">
                            <?php echo $deliveredOrders; ?>
                        </h1>
                    </div>
                </div>
            </div>

            <!-- Most Valued Sale -->
            <div class="col-md-3">
                <div class="card text-center border-warning">
                    <div class="card-header bg-warning text-white">
                        En Yüksek Tutarlı Sipariş <i class="bi bi-currency-dollar"></i>
                    </div>
                    <div class="card-body">
                        <h1 class="card-title">
                            <?php echo $mostValuedSalePrice; ?>
                        </h1>
                    </div>
                </div>
            </div>

            <!-- Total Orders -->
            <div class="col-md-3">
                <div class="card text-center border-primary">
                    <div class="card-header bg-primary text-white">
                        Toplam Sipariş <i class="bi bi-file-earmark"></i>
                    </div>
                    <div class="card-body">
                        <h1 class="card-title">
                            <?php echo $totalOrdersCount; ?>
                        </h1>
                    </div>
                </div>
            </div>

            <!-- Most Ordered Product -->
            <div class="col-md-3">
                <div class="card text-center border-danger">
                    <div class="card-header bg-danger text-white">
                        En Çok Satılan Ürün <i class="bi bi-file-earmark"></i>
                    </div>
                    <div class="card-body">
                        <h3 class="card-title">
                            <?php echo $mostOrderedProductName; ?> (<?php echo $mostOrderedProductCount; ?>)
                        </h3>

                    </div>
                </div>
            </div>
                



        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
