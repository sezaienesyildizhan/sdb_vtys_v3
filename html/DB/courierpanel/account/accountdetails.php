<?php

require('dbcon.php'); // Veri tabanı bağlantı dosyası dahil ediliyor.

// Kullanıcı oturum bilgisi kontrolü
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    echo "<script>window.location.href='/db/login.php';</script>";
    exit();
}

$username = $_SESSION['username'];

// Kullanıcı bilgilerini veritabanından çek
$query = "SELECT * FROM Users WHERE User_Username = ?";
$stmt = $connection->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "<p>Hesap bilgileri bulunamadı.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hesabım</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white px-lg-3 py-lg-2 shadow-sm sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand me-5 fw-bold fs-3 h-font" href="../sales.php">Courier Panel</a>
        <button class="navbar-toggler shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        </ul>
        <form class="d-flex">
            <a href="accountdetails.php" class="btn btn-outline-dark shadow-none me-lg-3 me-2">
               Hesabım
            </a>

            <a href="../../login.php" class="btn btn-outline-dark shadow-none me-lg-3 me-3">
               Çıkış Yap
            </a>
        </form>
        </div>
    </div>
    </nav>
<div class="container mt-5">
    <h2>Hesap Bilgileriniz</h2>
    <div class="card mt-4">
        <div class="card-body">
            <p><strong>Ad:</strong> <?php echo htmlspecialchars($user['User_Name']); ?></p>
            <p><strong>Soyad:</strong> <?php echo htmlspecialchars($user['User_Surname']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['User_Email']); ?></p>
            <p><strong>Telefon:</strong> <?php echo htmlspecialchars($user['User_PhoneNumber']); ?></p>
            <a href="updateaccount.php" class="btn btn-primary">Bilgileri Güncelle</a>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php require('../inc/footer.php'); ?>

</body>
</html>
