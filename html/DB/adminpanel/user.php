<?php

// Veritabanı bağlantısı
require_once('dbcon.php'); // dbcon.php dosyasını dahil et

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user_id'])) {
    // Silinecek kullanıcının ID'sini al
    $user_id_to_delete = intval($_POST['delete_user_id']);

    // Silme sorgusu
    $delete_query = "DELETE FROM Users WHERE User_ID = ?";
    $stmt = $connection->prepare($delete_query);
    $stmt->bind_param("i", $user_id_to_delete);

    if ($stmt->execute()) {
        echo "<script>alert('Kullanıcı başarıyla silindi!'); window.location.href='user.php';</script>";
    } else {
        echo "<script>alert('Kullanıcı silinemedi: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}


// Veritabanından kullanıcıları çek
$query = "SELECT u.User_ID, u.Role_ID, u.User_Name, u.User_Surname, u.User_Username, u.User_PhoneNumber, u.User_Email, u.User_Password, u.User_RegisteredAt, r.Role_Name
          FROM Users u 
          JOIN Roles r ON u.Role_ID = r.Role_ID";
$result = $connection->query($query);

if (!$result) {
    die("Veritabanı sorgusu başarısız: " . $connection->error);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcılar</title>
    <?php require('inc/links.php'); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
    <script src="js/export.js"></script>
</head>
<body>

<?php require('inc/header.php'); ?>
<!-- Header Alanı -->

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <h3 class="mb-3">Kullanıcılar</h3>
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
    <div id="toolbar">
        <button id="remove" class="btn btn-danger" disabled>
            <i class="fa fa-trash"></i> Seçili Satırları Sil
        </button>
    </div>

    <!-- Bootstrap Table -->
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
        data-detail-view="true"
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
                <th data-field="state" data-checkbox="true"></th>
                <th data-field="id" data-sortable="true">User ID</th>
                <th data-field="roleid" data-sortable="true">Role ID</th>
                <th data-field="rolename" data-sortable="true">Role</th>
                <th data-field="ad" data-sortable="true">Ad</th>
                <th data-field="soyad" data-sortable="true">Soyad</th>
                <th data-field="username" data-sortable="true">Kullanıcı Adı</th>
                
                <th data-field="sil">Kullanıcı Sil</th>
                <th data-field="guncelle">Kullanıcı Güncelle</th>
                <th data-field="detay">Kullanıcı Detay</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $result->fetch_assoc()) { ?>
                <tr>
                    <td></td>
                    <td><?php echo $user['User_ID']; ?></td>
                    <td><?php echo $user['Role_ID']; ?></td>
                    <td><?php echo $user['Role_Name']; ?></td>
                    <td><?php echo $user['User_Name']; ?></td>
                    <td><?php echo $user['User_Surname']; ?></td>
                    <td><?php echo $user['User_Username']; ?></td>
                    <td><form method="POST" onsubmit="return confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?');">
                    <input type="hidden" name="delete_user_id" value="<?php echo $user['User_ID']; ?>">
                    <button type="submit" class="btn btn-danger btn-sm">Sil</button>
                    </form></td>
                    <td><a href="user/userupdate.php?id=<?php echo $user['User_ID']; ?>" class="btn btn-warning">Güncelle</a></td>
                    <td><a href="user/userdetails.php?id=<?php echo $user['User_ID']; ?>" class="btn btn-info">Detay</a></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- Kullanıcı ve Rol Ekleme Butonları -->
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-6 mb-3">
            <a href="user/useradd.php" class="btn btn-primary w-100 text-center">Yeni Kullanıcı Ekle</a>
        </div>
        <div class="col-6">
            <a href="roles/listroles.php" class="btn btn-primary w-100 text-center">Rol Yönetimi</a>
        </div>
    </div>
</div>

<script>
    var $table = $('#table');
    var $remove = $('#remove');

    $(function () {
        // Tablonun başlangıç ayarları
        $table.bootstrapTable();

        // Sil butonuna tıklama olayını yönet
        $remove.click(function () {
            var ids = $.map($table.bootstrapTable('getSelections'), function (row) {
                return row.id;
            });
            alert('Seçili ID\'ler: ' + ids.join(', '));
            $table.bootstrapTable('remove', { field: 'id', values: ids });
            $remove.prop('disabled', true);
        });

        // Satır seçme olayları
        $table.on('check.bs.table uncheck.bs.table check-all.bs.table uncheck-all.bs.table', function () {
            $remove.prop('disabled', !$table.bootstrapTable('getSelections').length);
        });
    });

    // Export Butonuna Tıklama Olayı
    document.getElementById('exportButton').addEventListener('click', function () {
            const format = document.getElementById('exportFormat').value; // Format seçimi
            const fileName = "users"; // Dosya adı
            const tableSelector = "table"; // Hedef tablo seçicisi

        if (format === 'csv') {
            exportTableToCSV(tableSelector, fileName,6);
        } else if (format === 'xml') {
            exportTableToXML(tableSelector, fileName,6);
        } else if (format === 'txt') {
            exportTableToTXT(tableSelector, fileName,6);
        } else if (format === 'json') {
            exportTableToJSON(tableSelector, fileName,6);
        } else if (format === 'pdf') {
            exportTableToPDF(tableSelector, fileName,6);
        } else {
            alert("Bu format henüz desteklenmiyor!");
        }
    });
</script>

</body>
</html>
