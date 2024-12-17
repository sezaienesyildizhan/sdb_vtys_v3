<?php
require('dbcon.php'); // Veri tabanı bağlantı dosyası dahil ediliyor

// Veri tabanı bağlantısını kontrol et
if (!isset($connection) || !$connection) {
    die("Veritabanı bağlantısı başarısız: " . mysqli_connect_error());
}

mysqli_set_charset($connection, "utf8mb4");

// Aktiflik durumunu güncelleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['courier_id'], $_POST['action'])) {
    $courier_id = intval($_POST['courier_id']);
    $action = $_POST['action'] === 'activate' ? 1 : 0;

    $update_query = "UPDATE Couriers SET Courier_IsActive = $action WHERE Courier_ID = $courier_id";
    if (mysqli_query($connection, $update_query)) {
        echo "<script>alert('Kurye durumu başarıyla güncellendi.'); window.location.href='courier.php';</script>";
    } else {
        echo "<script>alert('Durum güncellenirken bir hata oluştu: " . mysqli_error($connection) . "');</script>";
    }
}




// Kuryeleri veritabanından çek
$query = "SELECT c.Courier_ID, u.User_Name AS Ad, u.User_Surname AS Soyad,
       u.User_Username AS KullanıcıAdı, u.User_PhoneNumber AS Telefon,
       c.Courier_IsActive AS Aktif, MAX(al.Action_Note) AS GörevDurumu
       FROM Couriers c
       JOIN Users u ON c.User_ID = u.User_ID
       LEFT JOIN ActionLog al ON c.Courier_ID = al.Courier_ID
       GROUP BY c.Courier_ID, u.User_Name, u.User_Surname, u.User_Username, u.User_PhoneNumber, c.Courier_IsActive;

";
$result = mysqli_query($connection, $query);

if (!$result) {
    die("Sorgu başarısız: " . mysqli_error($connection));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kuryeler</title>
    <?php require('inc/links.php'); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
    <script src="js/export.js"></script>
</head>
<body>
    <?php require('inc/header.php'); ?>
    <div class="container mt-5">
        

        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <h3 class="mb-0">Kuryeler</h3>
            <div class="d-flex">
                <select id="exportFormat" class="form-select me-2" style="min-width: 150px;">
                    <option value="xml">XML</option>
                    <option value="csv">Excel (CSV)</option>
                    <option value="pdf">PDF</option>
                    <option value="txt">TXT</option>
                    <option value="json">JSON</option>
                </select>
                <button id="exportButton" class="btn btn-success">Export</button>
            </div>
        </div>

        <table 
            id="table"
            data-toolbar="#toolbar"
            data-search="true"
            data-show-refresh="true"
            data-show-toggle="true"
            data-pagination="true"
            data-show-fullscreen="true"
            data-show-columns="true"
            data-show-columns-toggle-all="true"
            data-side-pagination="client"
            data-show-export="true"
            data-click-to-select="true"
            data-detail-formatter="detailFormatter"
            data-minimum-count-columns="2"
            data-show-pagination-switch="true"
            data-id-field="id"
            data-page-list="[10, 25, 50, 100, all]"
            data-show-footer="true"
            data-side-pagination="server"
            data-response-handler="responseHandler">
            <thead>
                <tr>
                    <th data-field="state"></th>
                    <th data-field="id" data-sortable="true">Kurye ID</th>
                    <th data-field="ad" data-sortable="true">Ad</th>
                    <th data-field="soyad" data-sortable="true">Soyad</th>
                    <th data-field="username" data-sortable="true">Kullanıcı Adı</th>
                    <th data-field="telefon" data-sortable="true">Telefon</th>
                    <th data-field="aktif" data-sortable="true">Aktif/Pasif</th>
                    <!-- <th data-field="gorevDurumu" data-sortable="true">Kurye Görev Durumu</th> -->
                    <th data-field="detay">Kurye Detay</th>
                    <th data-field="siparisler">Kurye Siparişleri</th>
                    <th data-field="aktiflik">Kurye Aktif Et</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td></td>
                    <td><?php echo $row['Courier_ID']; ?></td>
                    <td><?php echo $row['Ad']; ?></td>
                    <td><?php echo $row['Soyad']; ?></td>
                    <td><?php echo $row['KullanıcıAdı']; ?></td>
                    <td><?php echo $row['Telefon']; ?></td>
                    <td><?php echo ($row['Aktif'] == 1) ? 'Aktif' : 'Pasif'; ?></td>
                    <!-- <td><?php echo $row['GörevDurumu'] ?? 'Görev Yok'; ?></td> -->
                    <td>
                        <a href="courier/courierdetails.php?id=<?php echo $row['Courier_ID']; ?>" class="btn btn-info btn-sm">Detay</a>
                    </td>
                    <td>
                        <a href="courier/viewcouriersales.php?id=<?php echo $row['Courier_ID']; ?>" class="btn btn-primary btn-sm">Siparişler</a>
                    </td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="courier_id" value="<?php echo $row['Courier_ID']; ?>">
                            <input type="hidden" name="action" value="<?php echo $row['Aktif'] == 1 ? 'deactivate' : 'activate'; ?>">
                            <button 
                                type="submit" 
                                class="btn <?php echo $row['Aktif'] == 1 ? 'btn-danger' : 'btn-success'; ?> btn-sm">
                                <?php echo $row['Aktif'] == 1 ? 'Pasif Et' : 'Aktif Et'; ?>
                            </button>
                        </form>
                    </td>

                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>


    <form method="POST" id="delete-form">
       <input type="hidden" name="delete_ids" id="delete-ids">
    </form>

    <script>
    var $table = $('#table');
    var $remove = $('#remove');

    $(function () {
        $table.bootstrapTable();

        $remove.click(function () {
            var ids = $.map($table.bootstrapTable('getSelections'), function (row) {
                return row.id;
            });

            console.log('Silinecek ID\'ler:', ids);

            if (ids.length === 0) {
                alert('Silmek için bir satır seçin.');
                return;
            }

            if (confirm('Seçilen satırları silmek istediğinizden emin misiniz?')) {
                $('#delete-ids').val(JSON.stringify(ids));
                $('#delete-form').submit();
            }
        });

        $table.on('check.bs.table uncheck.bs.table check-all.bs.table uncheck-all.bs.table', function () {
            $remove.prop('disabled', !$table.bootstrapTable('getSelections').length);
        });
    });

    // Export Butonuna Tıklama Olayı
    document.getElementById('exportButton').addEventListener('click', function () {
            const format = document.getElementById('exportFormat').value; // Format seçimi
            const fileName = "kuryeler"; // Dosya adı
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

<?php
// Veritabanı bağlantısını kapat
mysqli_close($connection);
?>


