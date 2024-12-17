<?php
// Include database connection
include '../adminpanel/dbcon.php';

// Verify if the connection variable is set
if (!isset($connection) || !$connection) {
    die("Database connection error.");
}

// Kullanıcı oturum bilgisi kontrolü
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    echo "<script>window.location.href='/db/login.php';</script>";
    exit();
}

// Oturumdaki User_ID
$user_id = $_SESSION['user_id'];

// User_ID'ye karşılık gelen Courier_ID'yi al
$courierQuery = "SELECT Courier_ID FROM Couriers WHERE User_ID = ?";
$stmt = $connection->prepare($courierQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $courier_id = $result->fetch_assoc()['Courier_ID'];
} else {
    die("Kurye bilgisi bulunamadı.");
}

// Ödeme yöntemi değiştirme kısmı
if (isset($_POST['change_payment'], $_POST['sale_id'], $_POST['payment_method_id'])) {
    $sale_id = intval($_POST['sale_id']);
    $payment_method_id = intval($_POST['payment_method_id']);

    $update_payment_query = "UPDATE Payment SET PaymentMethod_ID = ? WHERE Sale_ID = ?";
    $stmt = $connection->prepare($update_payment_query);
    $stmt->bind_param("ii", $payment_method_id, $sale_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "<script>alert('Ödeme yöntemi başarıyla güncellendi.');</script>";
        } else {
            echo "<script>alert('Herhangi bir değişiklik yapılmadı.');</script>";
        }
    } else {
        error_log("SQL Hatası: " . $stmt->error);
        echo "<script>alert('Bir hata oluştu, lütfen tekrar deneyin.');</script>";
    }
    $stmt->close();
}

// if ($result->num_rows > 0) {
//     $courier_id = $result->fetch_assoc()['Courier_ID'];
// } else {
//     die("Kurye bilgisi bulunamadı.");
// }




// Tüm ödeme yöntemlerini getir
$paymentMethodsQuery = "SELECT PaymentMethod_ID, PaymentMethod_Name FROM PaymentMethods";
$paymentMethodsResult = $connection->query($paymentMethodsQuery);

$paymentMethods = [];
if ($paymentMethodsResult && $paymentMethodsResult->num_rows > 0) {
    while ($method = $paymentMethodsResult->fetch_assoc()) {
        $paymentMethods[] = $method;
    }
}

// Kuryeye ait siparişleri getir
$query = "SELECT 
    S.Sale_ID, 
    GROUP_CONCAT(P.Product_Name SEPARATOR ', ') AS Product_Names,
    S.Sale_TotalPrice, 
    SS.SaleStatu_Name, 
    S.Sale_AssignedDate, 
    S.Courier_ID, 
    PM.PaymentMethod_Name, 
    CONCAT(DL.DeliveryLocation_Latitude, ', ', DL.DeliveryLocation_Longitude) AS Location
    FROM Sales AS S
    INNER JOIN SaleStatus AS SS ON S.SaleStatu_ID = SS.SaleStatu_ID
    INNER JOIN SaleItems AS SI ON S.Sale_ID = SI.Sale_ID
    INNER JOIN Products AS P ON SI.Product_ID = P.Product_ID
    LEFT JOIN DeliveryLocation AS DL ON S.Sale_ID = DL.Sale_ID
    LEFT JOIN Payment AS PAY ON S.Sale_ID = PAY.Sale_ID
    LEFT JOIN PaymentMethods AS PM ON PAY.PaymentMethod_ID = PM.PaymentMethod_ID
    WHERE S.Courier_ID = ?
    GROUP BY 
        S.Sale_ID, S.Sale_TotalPrice, SS.SaleStatu_Name, 
        S.Sale_AssignedDate, S.Courier_ID, PM.PaymentMethod_Name, DL.DeliveryLocation_Latitude, DL.DeliveryLocation_Longitude
    ORDER BY S.Sale_ID;
";
$stmt = $connection->prepare($query);
$stmt->bind_param("i", $courier_id);
$stmt->execute();
$result = $stmt->get_result();
if (!$result) {
    die("Query failed: " . mysqli_error($connection));
}
?>


<?php 
// Siparişi "YOLDA" durumuna güncelle
if (isset($_POST['set_in_transit'], $_POST['sale_id'])) {
    $sale_id = intval($_POST['sale_id']);

    // Sale_ID ve Courier_ID kontrolü
    $check_query = "SELECT 1 FROM Sales WHERE Sale_ID = ? AND Courier_ID = ?";
    $check_stmt = $connection->prepare($check_query);
    $check_stmt->bind_param("ii", $sale_id, $courier_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // Sipariş durumunu "YOLDA" olarak güncelleyen SQL sorgusu
        $update_to_in_transit = "
            UPDATE Sales 
            SET SaleStatu_ID = (
                SELECT SaleStatu_ID 
                FROM SaleStatus 
                WHERE SaleStatu_Name = 'YOLDA'
            )
            WHERE Sale_ID = ? AND Courier_ID = ?
        ";

        $stmt = $connection->prepare($update_to_in_transit);
        $stmt->bind_param("ii", $sale_id, $courier_id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                // Başarı mesajı ve sayfa yenileme
                echo "<script>
                    alert('Sipariş başarıyla yola çıkarıldı.');
                    window.location.href = window.location.href;
                </script>";
            } else {
                // Değişiklik yoksa
                echo "<script>alert('Sipariş durumu zaten YOLDA.');</script>";
            }
        } else {
            // SQL hata loglama
            error_log('SQL Hatası: ' . $stmt->error);
            echo "<script>alert('Sipariş durumu güncellenirken bir hata oluştu.');</script>";
        }
        $stmt->close();
    } else {
        // Yetki veya sipariş kontrolü başarısız
        echo "<script>alert('Geçersiz sipariş ID veya yetkiniz yok.');</script>";
    }
    $check_stmt->close();
}
?>

<?php 
// Sipariş TESLIM EDILDI durumuna getirme
if (isset($_POST['deliver'], $_POST['sale_id'])) {
    $sale_id = intval($_POST['sale_id']);
    $action_type_id = 102; // ActionTypes tablosunda 'Teslim Edildi' ID'si

    // Veritabanı işlemleri için bir transaction başlat
    $connection->begin_transaction();

    try {
        // 1. Sales tablosunu güncelle (Sipariş durumu 'Teslim Edildi' yapılıyor)
        $update_query = "
            UPDATE Sales 
            SET SaleStatu_ID = (
                SELECT SaleStatu_ID 
                FROM SaleStatus 
                WHERE SaleStatu_Name = 'Teslim Edildi'
            ) 
            WHERE Sale_ID = ? AND Courier_ID = ?";
        $stmt = $connection->prepare($update_query);
        $stmt->bind_param("ii", $sale_id, $courier_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            // 2. DeliveryLocation tablosundan konum verisini al
            $delivery_location_query = "
                SELECT DeliveryLocation_Latitude, DeliveryLocation_Longitude
                FROM DeliveryLocation
                WHERE Sale_ID = ?";
            $stmt_location_select = $connection->prepare($delivery_location_query);
            $stmt_location_select->bind_param("i", $sale_id);
            $stmt_location_select->execute();
            $result_location = $stmt_location_select->get_result();

            if ($result_location->num_rows > 0) {
                $location_data = $result_location->fetch_assoc();
                $latitude = $location_data['DeliveryLocation_Latitude'];
                $longitude = $location_data['DeliveryLocation_Longitude'];

                // 3. Action Log tablosuna kayıt ekle
                $action_log_query = "
                    INSERT INTO ActionLog (Courier_ID, Action_Type_ID, Action_Note, Action_Time) 
                    VALUES (?, ?, 'Sipariş teslim edildi', NOW())";
                $stmt_action = $connection->prepare($action_log_query);
                $stmt_action->bind_param("ii", $courier_id, $action_type_id);
                $stmt_action->execute();

                // 4. Location Log tablosuna konum verisini ekle
                $location_log_query = "
                    INSERT INTO LocationLog (Courier_ID, Location_Latitude, Location_Longitude, Location_Time)
                    VALUES (?, ?, ?, NOW())";
                $stmt_location_insert = $connection->prepare($location_log_query);
                $stmt_location_insert->bind_param("idd", $courier_id, $latitude, $longitude);
                $stmt_location_insert->execute();

                // Transaction'u tamamla
                $connection->commit();
                // echo "<p class='text-success'>Sipariş başarıyla teslim edildi ve loglar güncellendi.</p>";
            } else {
                echo "<p class='text-danger'>Teslimat konum bilgisi bulunamadı.</p>";
                $connection->rollback();
            }

            // Statement'ları kapat
            $stmt_location_select->close();
            $stmt_action->close();
            $stmt_location_insert->close();
        } else {
            // echo "<p class='text-danger'>Siparişi teslim ederken bir hata oluştu veya zaten teslim edildi.</p>";
            $connection->rollback();
        }

        $stmt->close();

    } catch (Exception $e) {
        // Hata oluşursa işlemleri geri al
        $connection->rollback();
        error_log("Hata: " . $e->getMessage());
        echo "<p class='text-danger'>Bir hata oluştu: " . $e->getMessage() . "</p>";
    }
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siparişlerim</title>
    <?php require('inc/links.php'); ?>
    <style>
        
    </style>
</head>
<body>
    <?php require('inc/header.php'); ?>
    
   

    <h2 class="mt-5 pt-4 mb-4 text-center fw-bold h-font"> 
        Aktif Siparişler
    </h2>

        <!-- one table, one add button -->
        
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-table@1.23.5/dist/bootstrap-table.min.css">

<script src="https://cdn.jsdelivr.net/npm/bootstrap-table@1.23.5/dist/bootstrap-table.min.js"></script>

<div class="container mt-5">
    <br>
    <div class="table-responsive">
     <table class="table table-bordered table-hover text-center align-middle">
        <thead class="table-dark">
            <tr>
                <th scope="col">Sipariş ID</th>
                <th scope="col">Kurye ID</th>
                <th scope="col">Konum</th>
                <th scope="col">Sipariş Toplam Fiyatı</th>
                <th scope="col">Sipariş Durumu</th>
                <th scope="col">Sipariş Verilme Tarihi ve Zamanı</th>
                <th scope="col">Siparişi Teslim Et</th>
                <th scope="col">Ödeme Yöntemi</th>
                <th scope="col">Değiştir</th>

            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['Sale_ID'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['Courier_ID'] ?? ''); ?></td>
                        <td>
                           <form action="../adminpanel/courier/orderdetail.php" method="GET">
                                <input type="hidden" name="id" value="<?php echo $row['Sale_ID']; ?>">
                                
                                <button type="submit" class="btn btn-info btn-sm">Detaylar</button>
                                
                            </form>
                            
                        </td>
                        <td><?php echo htmlspecialchars($row['Sale_TotalPrice'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['SaleStatu_Name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['Sale_AssignedDate'] ?? ''); ?></td>
                        <td>
                            <?php if ($row['SaleStatu_Name'] === 'YOLDA'): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="sale_id" value="<?php echo $row['Sale_ID']; ?>">
                                    <button type="submit" name="deliver" class="btn btn-success btn-sm">Teslim Et</button>
                                </form> 
                            <?php elseif ($row['SaleStatu_Name'] === 'HAZIRLANIYOR'): ?> 
                                <!-- Siparişi Yola Çıkar Butonu -->
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="sale_id" value="<?php echo $row['Sale_ID']; ?>">
                                    <button type="submit" name="set_in_transit" class="btn btn-warning btn-sm">Yola Çıkar</button>
                                </form>
                            <?php elseif ($row['SaleStatu_Name'] === 'TESLIM EDILDI'): ?> 
                                <button type="" name="" class="btn btn-danger btn-sm">Teslim Edilmiş!</button>
                            <?php endif; ?>
                            


                        </td>
                        <td><?php echo htmlspecialchars($row['PaymentMethod_Name'] ?? ''); ?></td>

                        <td>
                         <form method="POST">
                                <input type="hidden" name="sale_id" value="<?php echo $row['Sale_ID']; ?>">
                                <select name="payment_method_id" class="btn btn-secondary dropdown-toggle">
                                    <?php foreach ($paymentMethods as $method): ?>
                                        <option value="<?= htmlspecialchars($method['PaymentMethod_ID']) ?>">
                                            <?= htmlspecialchars($method['PaymentMethod_Name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <br><br>
                                <button type="submit" name="change_payment" class="btn btn-info btn-sm">Değiştir</button>
                         </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">No data available</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>



    

    <br><br><br>

    <!-- Footer -->
    <?php require('inc/footer.php'); ?>


    <!-- js -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <!-- swiper -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        var swiper = new Swiper(".swiper-container", {
        spaceBetween: 30,
        effect: "fade",
        loop: true,
        autoplay: {
            delay: 3500,
            disableOnInteraction: false,
        }
        });
        var swiper = new Swiper(".swiper-testimonials", {
            effect: "coverflow",
            grabCursor: true,
            centeredSlides: true,
            slidesPerView: "auto",
            coverflowEffect: {
                rotate: 50,
                stretch: 0,
                depth: 100,
                modifier: 1,
                slideShadows: true,
            },
            pagination: {
                el: ".swiper-pagination",
            },
        });
    </script>
</body>
</html>