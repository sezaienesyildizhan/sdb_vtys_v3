<?php
// Veritabanı bağlantısı
require_once('dbcon.php'); // Veritabanı bağlantısını içerir

// Action Logs'u çek
$actionLogs = [];
$actionSql = "SELECT Action_LogID, Courier_ID, Action_Type_ID, Action_Note, Action_Time FROM ActionLog";
$actionResult = $connection->query($actionSql);
if ($actionResult->num_rows > 0) {
    while ($row = $actionResult->fetch_assoc()) {
        $actionLogs[] = $row;
    }
}

// Location Logs'u çek
$locationLogs = [];
$locationSql = "SELECT Location_LogID, Courier_ID, Location_Latitude, Location_Longitude, Location_Time FROM LocationLog";
$locationResult = $connection->query($locationSql);
if ($locationResult->num_rows > 0) {
    while ($row = $locationResult->fetch_assoc()) {
        $locationLogs[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Kayıtları</title>
    <?php require('inc/links.php'); ?> <!-- Bootstrap CSS -->
</head>
<body>
    <?php require('inc/header.php'); ?> <!-- Navbar -->

    <div class="container mt-5">
        <h2 class="text-center mb-4">Log Kayıtları</h2>

        <!-- Sekmeler -->
        <ul class="nav nav-tabs mb-3" id="logTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="action-log-tab" data-bs-toggle="tab" data-bs-target="#action-log" type="button" role="tab">
                    Action Logs
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="location-log-tab" data-bs-toggle="tab" data-bs-target="#location-log" type="button" role="tab">
                    Location Logs
                </button>
            </li>
        </ul>

        <!-- Sekme İçerikleri -->
        <div class="tab-content" id="logTabsContent">
            <!-- Action Logs -->
            <div class="tab-pane fade show active" id="action-log" role="tabpanel">
                <div class="mb-3">
                    <input type="text" id="actionSearch" class="form-control" placeholder="Action Loglarda Ara...">
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>Log ID</th>
                                <th>Kurye ID</th>
                                <th>Action Türü</th>
                                <th>Not</th>
                                <th>Zaman</th>
                            </tr>
                        </thead>
                        <tbody id="actionTableBody">
                            <?php foreach ($actionLogs as $log) { ?>
                                <tr>
                                    <td><?php echo $log['Action_LogID']; ?></td>
                                    <td><?php echo $log['Courier_ID']; ?></td>
                                    <td><?php echo $log['Action_Type_ID']; ?></td>
                                    <td><?php echo htmlspecialchars($log['Action_Note']); ?></td>
                                    <td><?php echo $log['Action_Time']; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Location Logs -->
            <div class="tab-pane fade" id="location-log" role="tabpanel">
                <div class="mb-3">
                    <input type="text" id="locationSearch" class="form-control" placeholder="Location Loglarda Ara...">
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>Log ID</th>
                                <th>Kurye ID</th>
                                <th>Enlem</th>
                                <th>Boylam</th>
                                <th>Zaman</th>
                            </tr>
                        </thead>
                        <tbody id="locationTableBody">
                            <?php foreach ($locationLogs as $log) { ?>
                                <tr>
                                    <td><?php echo $log['Location_LogID']; ?></td>
                                    <td><?php echo $log['Courier_ID']; ?></td>
                                    <td><?php echo $log['Location_Latitude']; ?></td>
                                    <td><?php echo $log['Location_Longitude']; ?></td>
                                    <td><?php echo $log['Location_Time']; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Arama Özelliği -->
    <script>
        document.getElementById('actionSearch').addEventListener('input', function () {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('#actionTableBody tr');
            rows.forEach(row => {
                const rowData = row.innerText.toLowerCase();
                row.style.display = rowData.includes(searchValue) ? '' : 'none';
            });
        });

        document.getElementById('locationSearch').addEventListener('input', function () {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('#locationTableBody tr');
            rows.forEach(row => {
                const rowData = row.innerText.toLowerCase();
                row.style.display = rowData.includes(searchValue) ? '' : 'none';
            });
        });
    </script>
</body>
</html>