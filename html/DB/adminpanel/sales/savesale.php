<?php
require('../dbcon.php');

// POST verilerini al
$sale_id = isset($_POST['sale_id']) ? intval($_POST['sale_id']) : 0;

$latitude = isset($_POST['latitude']) ? trim($_POST['latitude']) : '';
$longitude = isset($_POST['longitude']) ? trim($_POST['longitude']) : '';
$paymentMethod = isset($_POST['paymentMethod']) ? intval($_POST['paymentMethod']) : 0;
$courierID = isset($_POST['courierID']) ? intval($_POST['courierID']) : 0;
$totalPrice = isset($_POST['totalPrice']) ? floatval($_POST['totalPrice']) : 0;
$productIDs = isset($_POST['productID']) ? $_POST['productID'] : [];
$quantities = isset($_POST['quantity']) ? $_POST['quantity'] : [];
$saleStatusID = isset($_POST['saleStatusID']) ? intval($_POST['saleStatusID']) : 0;


// total price için ürün fiyatı * miktar. productIDs ve quantities dizilerini kullanarak totalPrice hesapla
$totalPrice = 0;
for ($i = 0; $i < count($productIDs); $i++) {
    $productID = intval($productIDs[$i]);
    $quantity = intval($quantities[$i]);

    $productQuery = "SELECT Product_Price FROM Products WHERE Product_ID = $productID";
    $result = mysqli_query($connection, $productQuery);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $totalPrice += $row['Product_Price'] * $quantity;
    }
}


if ($sale_id > 0) {
    // 1. Sales tablosunu güncelle
    $updateSaleQuery = "UPDATE Sales SET Courier_ID = $courierID WHERE Sale_ID = $sale_id";
    mysqli_query($connection, $updateSaleQuery);

    // 1.1.Sale_ID ile SaleTotalPrice'i güncelle
    $updateSaleTotalPriceQuery = "UPDATE Sales SET Sale_TotalPrice = $totalPrice WHERE Sale_ID = $sale_id";
    mysqli_query($connection, $updateSaleTotalPriceQuery);

    // 1.2.SaleStatu_ID'yi güncelle
    $updateSaleStatusQuery = "UPDATE Sales SET SaleStatu_ID = $saleStatusID WHERE Sale_ID = $sale_id";
    mysqli_query($connection, $updateSaleStatusQuery);

    // 2. Payment tablosunu güncelle
    $updatePaymentQuery = "UPDATE Payment SET PaymentMethod_ID = $paymentMethod, Payment_Amount = $totalPrice WHERE Sale_ID = $sale_id";
    mysqli_query($connection, $updatePaymentQuery);

    // 3. DeliveryLocation tablosunu güncelle
    $updateDeliveryQuery = "UPDATE DeliveryLocation SET DeliveryLocation_Latitude = '$latitude', DeliveryLocation_Longitude = '$longitude' WHERE Sale_ID = $sale_id";
    mysqli_query($connection, $updateDeliveryQuery);

    // 4. SaleItems karşılaştırma ve güncelleme
    // Veritabanından mevcut ürünleri çek
    $oldProducts = [];
    $saleItemsQuery = "SELECT Product_ID, Quantity FROM SaleItems WHERE Sale_ID = $sale_id";
    $result = mysqli_query($connection, $saleItemsQuery);

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $oldProducts[$row['Product_ID']] = $row['Quantity'];
        }
    }

    // Yeni ürünleri POST verisinden al
    $newProducts = [];
    for ($i = 0; $i < count($productIDs); $i++) {
        $productID = intval($productIDs[$i]);
        $quantity = intval($quantities[$i]);
        $newProducts[$productID] = $quantity;
    }

    // 4.1 Eskide olup yeni listede olmayanları sil
    foreach ($oldProducts as $productID => $quantity) {
        if (!array_key_exists($productID, $newProducts)) {
            $deleteQuery = "DELETE FROM SaleItems WHERE Sale_ID = $sale_id AND Product_ID = $productID";
            mysqli_query($connection, $deleteQuery);
        }
    }

    // 4.2 Yeni listede olup eskide olmayanları ekle
    foreach ($newProducts as $productID => $quantity) {
        if (!array_key_exists($productID, $oldProducts)) {
            $insertQuery = "INSERT INTO SaleItems (Sale_ID, Product_ID, Quantity) 
                            VALUES ($sale_id, $productID, $quantity)";
            mysqli_query($connection, $insertQuery);
        }
    }

    // 4.3 Ortak ürünlerde miktarı güncelle
    foreach ($newProducts as $productID => $quantity) {
        if (array_key_exists($productID, $oldProducts) && $oldProducts[$productID] != $quantity) {
            $updateQuery = "UPDATE SaleItems SET Quantity = $quantity 
                            WHERE Sale_ID = $sale_id AND Product_ID = $productID";
            mysqli_query($connection, $updateQuery);
        }
    }




    // Güncelleme başarılıysa sales.php'ye yönlendir
    // header("Location: ../sales.php");
    echo "<script>window.location.href='/db/adminpanel/sales.php';</script>";
    exit;
} else {
    // Geçersiz ID için hata mesajı
    echo "Geçersiz Satış ID'si.";
    exit;
}
?>
