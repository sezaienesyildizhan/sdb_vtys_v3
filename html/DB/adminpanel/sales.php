<?php
require('dbcon.php'); // Veritabanı bağlantısı

// Siparişleri veritabanından çek
$sales = [];
$salesQuery = "
    SELECT 
    Sales.Sale_ID, 
    Sales.Sale_IsActive, 
    Sales.Courier_ID, 
    Users.User_Name AS Courier_Name, 
    Sales.Sale_AssignedDate, 
    Sales.Sale_TotalPrice, 
    SaleStatus.SaleStatu_Name AS SaleStatusName
    
    FROM Sales
    LEFT JOIN Couriers ON Sales.Courier_ID = Couriers.Courier_ID
    LEFT JOIN Users ON Couriers.User_ID = Users.User_ID
    LEFT JOIN SaleStatus ON Sales.SaleStatu_ID = SaleStatus.SaleStatu_ID;

";
$result = mysqli_query($connection, $salesQuery);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $sales[] = $row;
    }
} else {
    die("Veritabanı sorgu hatası: " . mysqli_error($connection));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siparişler</title>
    <?php require('inc/links.php'); ?>

    <!-- for export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>

    <script src="js/export.js"></script>
</head>
<body>
    <?php require('inc/header.php'); ?>

    <div class="container mt-5">
        <!-- Başlık -->
        <h2 class="mb-4 text-center">Tüm Siparişler</h2>

        <!-- Arama Alanı -->
        <div class="row mb-3">
            <div class="col-md-4">
                <input type="text" id="searchKuryeName" class="form-control" placeholder="Kurye Adına Göre Ara...">
            </div>
            <div class="col-md-4">
                <select id="exportFormat" class="form-select">
                    <option value="xml">XML</option>
                    <option value="csv">Excel (CSV)</option>
                    <option value="pdf">PDF</option>
                    <option value="txt">TXT</option>
                    <option value="json">JSON</option>
                </select>
            </div>
            <div class="col-md-2">
                <button id="exportButton" class="btn btn-success">Export</button>
            </div>
        </div>

        <!-- Siparişler Tablosu -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover text-center align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Sipariş ID</th>
                        <th>Aktiflik</th>
                        <th>Kurye ID</th>
                        <th>Kurye Adı</th>
                        <th>Atanma Tarihi</th>
                        <th>Toplam Fiyat</th>
                        <th>Durum</th>
                        <th>Aksiyonlar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($sales)): ?>
                        <?php foreach ($sales as $sale): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($sale['Sale_ID']); ?></td>
                                <td><?php echo $sale['Sale_IsActive'] ? 'Aktif' : 'Pasif'; ?></td>
                                <td><?php echo htmlspecialchars($sale['Courier_ID']); ?></td>
                                <td><?php echo htmlspecialchars($sale['Courier_Name']); ?></td>
                                <td><?php echo htmlspecialchars($sale['Sale_AssignedDate']); ?></td>
                                <td><?php echo htmlspecialchars(number_format($sale['Sale_TotalPrice'], 2)) . ' ₺'; ?></td>
                                <td><?php echo htmlspecialchars($sale['SaleStatusName']); ?></td>
                                <td>
                                    <a href="courier/orderdetail.php?id=<?php echo $sale['Sale_ID']; ?>" class="btn btn-info btn-sm">Detay</a>
                                    <a href="sales/deletesales.php?id=<?php echo (int)$sale['Sale_ID']; ?>" 
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Bu siparişi silmek istediğinize emin misiniz?');">
                                    İptal
                                    </a>
                                    <a href="sales/updatesales.php?id=<?php echo $sale['Sale_ID']; ?>" class="btn btn-warning btn-sm">Düzenle</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8">Henüz sipariş bulunmamaktadır.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Sipariş Ekle Butonu -->
            <a href="sales/addsales.php" class="btn btn-primary">Sipariş Ekle</a>
        </div>

    </div>

    <!-- Bootstrap ve jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

    <!-- Kurye Adına Göre Arama Scripti -->
    <script>
        document.getElementById('searchKuryeName').addEventListener('input', function () {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const kuryeName = row.cells[3].innerText.toLowerCase(); // Kurye Adı sütunu (4. sütun)
                row.style.display = kuryeName.includes(searchValue) ? '' : 'none';
            });
        });

        


        // Export Butonuna Tıklama Olayı
        document.getElementById('exportButton').addEventListener('click', function () {
            const format = document.getElementById('exportFormat').value; // Format seçimi
            const fileName = "siparisler"; // Dosya adı
            const tableSelector = "table"; // Hedef tablo seçicisi

            if (format === 'csv') {
                exportTableToCSV(tableSelector, fileName,7);
            } else if (format === 'xml') {
                exportTableToXML(tableSelector, fileName,7);
            } else if (format === 'txt') {
                exportTableToTXT(tableSelector, fileName,7);
            } else if (format === 'json') {
                exportTableToJSON(tableSelector, fileName,7);
            } else if (format === 'pdf') {
                exportTableToPDF(tableSelector, fileName,7);
            } else {
                alert("Bu format henüz desteklenmiyor!");
            }
        });


    </script>
</body>
</html>
