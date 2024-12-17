<?php
require('../dbcon.php'); // Veritabanı bağlantısı kontrolü

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_sales'])) {
    // Form verilerini al
    $productIDs = $_POST['productID'];
    $quantities = $_POST['quantity'];
    $courierID = $_POST['courierID'];
    $paymentMethodID = $_POST['paymentMethod']; // PaymentMethod_ID
    $totalPrice = (float) $_POST['totalPrice'];
    $latitude = (float) $_POST['latitude'];
    $longitude = (float) $_POST['longitude'];
    $saleStatusID = (int) $_POST['saleStatusID'];
    $paymentStatusID = (int) $_POST['paymentStatusID'];


    // Hataları kontrol et
    if (empty($productIDs) || empty($quantities) || empty($courierID) || empty($paymentMethodID) || empty($totalPrice) || empty($latitude) || empty($longitude)) {
        echo "Lütfen tüm alanları doldurun.";
        exit;
    }

    // Transaction başlat
    $connection->begin_transaction();
    try {
        // 1. Sales tablosuna satış ekle
        $insertSaleQuery = "INSERT INTO Sales (Courier_ID, Sale_IsActive, Sale_TotalPrice, Sale_AssignedDate) 
                            VALUES (?, 1, ?, NOW())";
        $stmt = $connection->prepare($insertSaleQuery);
        $stmt->bind_param('id', $courierID, $totalPrice);
        $stmt->execute();
        $saleID = $stmt->insert_id; // Eklenen satış ID'si
        $saleTotalPrice = $totalPrice;

        // 1.1.sale id nin statusunu ayarla
        $updateSaleStatusQuery = "UPDATE Sales SET SaleStatu_ID = ? WHERE Sale_ID = ?";
        $stmt = $connection->prepare($updateSaleStatusQuery);
        $stmt->bind_param('ii', $saleStatusID, $saleID);
        $stmt->execute();


        // 2. SaleItems tablosuna ürünleri ekle
        $insertSaleItemQuery = "INSERT INTO SaleItems (Sale_ID, Product_ID, Quantity) VALUES (?, ?, ?)";
        $stmt = $connection->prepare($insertSaleItemQuery);
        foreach ($productIDs as $key => $productID) {
            $quantity = $quantities[$key];
            $stmt->bind_param('iii', $saleID, $productID, $quantity);
            $stmt->execute();
        }

        // 3. Payment tablosuna ödeme ekle
        $insertPaymentQuery = "INSERT INTO Payment (Sale_ID, Payment_Amount, PaymentMethod_ID, Payment_Time) 
                               VALUES (?, ?, ?, NOW())";
        $stmt = $connection->prepare($insertPaymentQuery);
        $stmt->bind_param('idi', $saleID, $saleTotalPrice, $paymentMethodID);
        $stmt->execute();

        // 3.5. PaymentStatus tablosunda PaymentStatus_ID ve PaymentStatus_Name var. 1 için "Beklemede", 2 için "Tamamlandi", 3 için "İptal Edildi" gibi.
        // SaleID'ye göre Payment Tablosundaki PaymentStatus_ID'yi güncelle
        $updatePaymentStatusQuery = "UPDATE Payment SET PaymentStatus_ID = ? WHERE Sale_ID = ?";
        $stmt = $connection->prepare($updatePaymentStatusQuery);
        $stmt->bind_param('ii', $paymentStatusID, $saleID);
        $stmt->execute();


        // 4. DeliveryLocation tablosuna teslimat adresini ekle
        $insertDeliveryQuery = "INSERT INTO DeliveryLocation (Sale_ID, DeliveryLocation_Latitude, DeliveryLocation_Longitude) 
                                VALUES (?, ?, ?)";
        $stmt = $connection->prepare($insertDeliveryQuery);
        $stmt->bind_param('idd', $saleID, $latitude, $longitude);
        $stmt->execute();


        // Transaction onayla
        $connection->commit();
        echo "<script>window.location.href='/db/adminpanel/sales.php';</script>";
        // echo "Sipariş başarıyla kaydedildi.";
    } catch (Exception $e) {
        // Hata durumunda geri al
        // hatayı yazdır
        print_r($e);
        $connection->rollback();

        // echo "Sipariş eklenirken bir hata oluştu: " . $e->getMessage();
    }
} else {
    // echo "Geçersiz istek.";
}
?>
