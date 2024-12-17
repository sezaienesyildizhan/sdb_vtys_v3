<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ödeme Detayları</title>
    <?php require('../inc/links.php'); ?>
</head>
<body>

    <!-- Header -->
    <?php require('../inc/header.php'); ?>

    <div class="container">
        <h2>Ödeme Detayları</h2>
        
        <!-- Ödeme ID -->
        <div class="form-group">
            <label for="payment_id" class="fw-bold">Ödeme ID:</label>
            <span id="payment_id">1</span>
        </div>
        
        <!-- Ödeme Yöntemi -->
        <div class="form-group">
            <label for="payment_method" class="fw-bold">Ödeme Yöntemi:</label>
            <span id="payment_method">Kart</span>
        </div>
        
        <!-- Ödeme Alınma Tarihi -->
        <div class="form-group">
            <label for="payment_date" class="fw-bold">Ödeme Alınma Tarihi:</label>
            <span id="payment_date">2024-12-10 15:00:00</span>
        </div>
        
        <!-- Ödeme Tutarı -->
        <div class="form-group">
            <label for="payment_amount" class="fw-bold">Ödeme Tutarı (TL):</label>
            <span id="payment_amount">120.00</span>
        </div>

    </div>

</body>
</html>
