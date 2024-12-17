<?php
require('../dbcon.php'); // Veritabanı bağlantısı

if (isset($_GET['id'])) { // GET üzerinden Sale_ID kontrolü
    $saleID = intval($_GET['id']); // GET ile gelen Sale_ID'yi tam sayı yap
    
    // DEBUGGING: Gelen ID'yi log dosyasına yaz
    error_log("Gelen ID: " . $saleID);

    // Transaction başlat
    $connection->begin_transaction();

    try {
        // 1. SaleItems sil
        $deleteSaleItemsQuery = "DELETE FROM SaleItems WHERE Sale_ID = ?";
        $stmt = $connection->prepare($deleteSaleItemsQuery);
        $stmt->bind_param('i', $saleID);
        $stmt->execute();

        // 2. Payment sil
        $deletePaymentQuery = "DELETE FROM Payment WHERE Sale_ID = ?";
        $stmt = $connection->prepare($deletePaymentQuery);
        $stmt->bind_param('i', $saleID);
        $stmt->execute();

        // 3. DeliveryLocation sil
        $deleteDeliveryQuery = "DELETE FROM DeliveryLocation WHERE Sale_ID = ?";
        $stmt = $connection->prepare($deleteDeliveryQuery);
        $stmt->bind_param('i', $saleID);
        $stmt->execute();

        // 4. Sales sil
        $deleteSalesQuery = "DELETE FROM Sales WHERE Sale_ID = ?";
        $stmt = $connection->prepare($deleteSalesQuery);
        $stmt->bind_param('i', $saleID);
        $stmt->execute();

        // Transaction onayla
        $connection->commit();

        // Yönlendirme: header() fonksiyonundan önce çıktı olmadığından emin olun
        echo "<script>window.location.href='/db/adminpanel/sales.php';</script>";
        exit;
    } catch (Exception $e) {
        // Hata durumunda rollback yap
        $connection->rollback();
        echo "Silme işlemi sırasında bir hata oluştu: " . $e->getMessage();
    }
} else {
    echo "Geçersiz istek.";
}
?>
