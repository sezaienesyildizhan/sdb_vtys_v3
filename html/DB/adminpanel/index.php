<?php
// Dashbord queries
include ("../adminpanel/dbcon.php");

// 1. Toplam Kurye Sayısı
$totalCourierQuery = "SELECT COUNT(*) AS ToplamKurye FROM Couriers;";
$totalCourier = $connection->query($totalCourierQuery)->fetch_assoc()['ToplamKurye'];



// 3. Teslim Edilen Sipariş Sayısı
$deliveredOrdersQuery = "SELECT COUNT(*) AS TeslimEdilen
                         FROM Sales s
                         JOIN SaleStatus ss ON s.SaleStatu_ID = ss.SaleStatu_ID
                         WHERE ss.SaleStatu_Name = 'TESLIM EDILDI';";

$deliveredOrders = $connection->query($deliveredOrdersQuery)->fetch_assoc()['TeslimEdilen'];

// 4. Dağıtımda Olan Kurye Sayısı
$inDeliveryQuery = "SELECT COUNT(DISTINCT Courier_ID) AS DagitimdaOlan 
                    FROM Sales s 
                    JOIN SaleStatus ss ON s.SaleStatu_ID = ss.SaleStatu_ID 
                    WHERE ss.SaleStatu_Name = 'YOLDA';";
$inDelivery = $connection->query($inDeliveryQuery)->fetch_assoc()['DagitimdaOlan'];


// 5. En Çok Satış Yapan Kurye ismi ve satış sayısı
$mostSalesCourierQuery = "SELECT 
                        u.User_Name AS KuryeAdi, 
                        COUNT(s.Sale_ID) AS ToplamSatis
                        FROM Sales s
                        JOIN Couriers c ON s.Courier_ID = c.Courier_ID
                        JOIN Users u ON c.User_ID = u.User_ID
                        GROUP BY u.User_Name
                        ORDER BY ToplamSatis DESC
                        LIMIT 1;";

$mostSalesCourier = $connection->query($mostSalesCourierQuery)->fetch_assoc();

$mostSalesCourierName = $mostSalesCourier['KuryeAdi'];
$mostSalesCourierCount = $mostSalesCourier['ToplamSatis'];

// 6. En çok gelir getiren sipariş

$mostValuedSalesQuery = "SELECT MAX(Sale_TotalPrice) AS EnYuksekTutar 
                         FROM Sales;";

$mostValuedSale = $connection->query($mostValuedSalesQuery)->fetch_assoc();

$mostValuedSalePrice = $mostValuedSale['EnYuksekTutar'];




// 7. Toplam sipariş

$totalOrdersQuery = "SELECT COUNT(Sale_ID) AS ToplamSatis
                     FROM Sales";

$totalOrders = $connection->query($totalOrdersQuery)->fetch_assoc();
$totalOrdersCount = $totalOrders["ToplamSatis"];




// 8. İptal Olan sipariş




// 9. En çok satılan ürün

$mostOrderedProductQuery = "SELECT 
                            p.Product_Name AS UrunAdi, 
                            SUM(si.Quantity) AS ToplamSatisMiktari
                            FROM SaleItems si
                            JOIN Products p ON si.Product_ID = p.Product_ID
                            GROUP BY p.Product_Name
                            ORDER BY ToplamSatisMiktari DESC
                            LIMIT 1";

$mostOrderedProduct_ = $connection->query($mostOrderedProductQuery)->fetch_assoc();
$mostOrderedProductName = $mostOrderedProduct_['UrunAdi'];
$mostOrderedProductCount = $mostOrderedProduct_['ToplamSatisMiktari'];





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
