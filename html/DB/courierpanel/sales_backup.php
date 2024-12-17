<?php
// Include database connection
include '../adminpanel/dbcon.php';

// Verify if the connection variable is set
if (!isset($connection) || !$connection) {
    die("Database connection error.");
}

// Check if delivery action is triggered
if (isset($_POST['deliver']) && isset($_POST['sale_id'])) {
    $sale_id = intval($_POST['sale_id']);
    $update_query = "UPDATE Sales SET SaleStatu_ID = (SELECT SaleStatu_ID FROM SaleStatus WHERE SaleStatu_Name = 'Teslim Edildi') WHERE Sale_ID = $sale_id";
    mysqli_query($connection, $update_query);
    if (mysqli_error($connection)) {
        die("Failed to update sale status: " . mysqli_error($connection));
    }
}

// Fetch sales data
$query = "SELECT Sales.Sale_ID, Couriers.Courier_ID, CONCAT(DeliveryLocation.DeliveryLocation_Latitude, ', ', DeliveryLocation.DeliveryLocation_Longitude) AS Location, 
                 Sales.Sale_TotalPrice, SaleStatus.SaleStatu_Name, Sales.Sale_AssignedDate
          FROM Sales
          LEFT JOIN Couriers ON Sales.Courier_ID = Couriers.Courier_ID
          LEFT JOIN DeliveryLocation ON Sales.Sale_ID = DeliveryLocation.Sale_ID
          LEFT JOIN SaleStatus ON Sales.SaleStatu_ID = SaleStatus.SaleStatu_ID";
$result = mysqli_query($connection, $query);
if (!$result) {
    die("Query failed: " . mysqli_error($connection));
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Satışlar</title>
    <?php require('inc/links.php'); ?>
</head>
<body>
    
    <!-- one table, one add button -->
    <?php require('inc/header.php'); ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-table@1.23.5/dist/bootstrap-table.min.css">

<script src="https://cdn.jsdelivr.net/npm/bootstrap-table@1.23.5/dist/bootstrap-table.min.js"></script>

<div class="container mt-5">
    <h2 class="mb-4">Sales Data</h2>
    <table class="table table-striped">
        <thead class="thead-dark">
            <tr>
                <th scope="col">Sipariş ID</th>
                <th scope="col">Kurye ID</th>
                <th scope="col">Konum</th>
                <th scope="col">Sipariş Toplam Fiyatı</th>
                <th scope="col">Sipariş Durumu</th>
                <th scope="col">Sipariş Verilme Tarihi ve Zamanı</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['Sale_ID'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['Courier_ID'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['Location'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['Sale_TotalPrice'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['SaleStatu_Name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['Sale_AssignedDate'] ?? ''); ?></td>
                        <td>
                            <?php if ($row['SaleStatu_Name'] === 'Beklemede'): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="sale_id" value="<?php echo $row['Sale_ID']; ?>">
                                    <button type="submit" name="deliver" class="btn btn-success btn-sm">Teslim Et</button>
                                </form>
                            <?php endif; ?>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>