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

// Kullanıcı bilgilerini güncelle
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    $update_query = "UPDATE Users SET User_Name = ?, User_Surname = ?, User_Email = ?, User_PhoneNumber = ? WHERE User_Username = ?";
    $update_stmt = $connection->prepare($update_query);
    $update_stmt->bind_param("sssss", $name, $surname, $email, $phone, $username);
    $update_stmt->execute();

    if ($update_stmt->affected_rows > 0) {
        echo "<p class='text-success'>Bilgileriniz başarıyla güncellendi.</p>";
        echo "<script>window.location.href='/db/courierpanel/account/accountdetails.php';</script>";

    } else {
        echo "<p class='text-danger'>Bilgileriniz güncellenemedi.</p>";
    }

    $update_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bilgileri Güncelle</title>
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

            <a href="../login.php" class="btn btn-outline-dark shadow-none me-lg-3 me-3">
               Çıkış Yap
            </a>
        </form>
        </div>
    </div>
    </nav>
<div class="container mt-5">
    <h2>Bilgilerinizi Güncelleyin</h2>
    <form method="POST" action="">
        <div class="mb-3">
            <label for="name" class="form-label">Ad</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['User_Name']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="surname" class="form-label">Soyad</label>
            <input type="text" class="form-control" id="surname" name="surname" value="<?php echo htmlspecialchars($user['User_Surname']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['User_Email']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Telefon</label>
            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['User_PhoneNumber']); ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Güncelle</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<?php require('../inc/footer.php'); ?>

</body>
</html>
