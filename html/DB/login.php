<?php

require('dbcon.php'); // Veri tabanı bağlantı dosyası dahil ediliyor.

// Veri tabanı bağlantısı kontrolü
if (!isset($connection) || !$connection) {
    die("Veri tabanı bağlantısı başarısız: " . mysqli_connect_error());
}

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_username = $_POST['username'];
    $user_password = $_POST['password'];

    // SQL Injection'a karşı güvenli sorgu
    $stmt = $connection->prepare("SELECT User_ID, User_Username, Role_ID FROM Users WHERE User_Username = ? AND User_Password = ?");
    $stmt->bind_param("ss", $user_username, $user_password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Kullanıcı doğrulandı
        $row = $result->fetch_assoc();
        $user_id = $row['User_ID'];
        $role_id = $row['Role_ID'];
        $username = $row['User_Username'];

        // Kullanıcı bilgilerini oturuma ve cookie'ye kaydet
        $_SESSION['logged_in'] = true;
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['role_id'] = $role_id;


        // Role_ID'ye göre yönlendirme
        if ($role_id == 1) { // Admin için
            echo "<script>window.location.href='/db/adminpanel/index.php';</script>";
            exit();
        } elseif ($role_id == 2) { // Kurye için
            echo "<script>window.location.href='/db/courierpanel/sales.php';</script>";
            exit();
        }
    } else {
        $error_message = "Geçersiz kullanıcı adı veya şifre.";
    }
    $stmt->close();
}

$connection->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-image: url('back.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        .navbar {
            position: absolute;
            top: 0;
            width: 100%;
        }
        .card {
            width: 100%;
            max-width: 400px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            background-color: rgba(255, 255, 255, 0.9); 
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Restoran Kurye Takip Paneli</a>
    </div>
</nav>

<div class="card">
    <div class="card-header text-center">
        <h3>Giriş Yap</h3>
    </div>
    <div class="card-body">

        <?php if ($error_message): ?>
            <div class="alert alert-danger text-center">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>
        
        <form action="login.php" method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Kullanıcı Adı</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Şifre</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Giriş Yap</button>
            </div>     
        </form>

        
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
