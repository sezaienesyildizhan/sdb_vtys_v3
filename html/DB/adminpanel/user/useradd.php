<?php


// Veritabanı bağlantısı
require_once('../dbcon.php'); // dbcon.php dosyasını dahil et

// Kullanıcı ekleme işlemi
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Formdan gelen verileri al
    $user_name = $_POST['name'];
    $user_surname = $_POST['surname'];
    $user_username = $_POST['username'];
    $user_role = $_POST['role'];
    $user_phone = $_POST['phone'];
    $user_email = $_POST['email'];
    $user_password = $_POST['password'];

    // Kullanıcıyı ekle
    $query = "INSERT INTO Users (User_Name, User_Surname, User_Username, Role_ID, User_PhoneNumber, User_Email, User_Password) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $connection->prepare($query);
    $stmt->bind_param("sssiiss", $user_name, $user_surname, $user_username, $user_role, $user_phone, $user_email, $user_password);

    if ($stmt->execute()) {
        $new_user_id = $connection->insert_id; // Yeni eklenen kullanıcının ID'si
    
        // Kullanıcı rolü kurye ise Couriers tablosuna ekle
        if ($user_role == 2) { // Role_ID 2 = Courier
            $courier_query = "INSERT INTO Couriers (User_ID, Courier_IsActive) VALUES (?, ?)";
            $courier_stmt = $connection->prepare($courier_query);
            $courier_is_active = 1; // Varsayılan aktif durumu
            $courier_stmt->bind_param("ii", $new_user_id, $courier_is_active);
    
            if (!$courier_stmt->execute()) {
                // Hata mesajı
                echo "<script>alert('Kurye eklenirken bir hata oluştu: " . $connection->error . "');</script>";
            }
        }
    
        // Başarılıysa yönlendir
        echo "<script>window.location.href='/db/adminpanel/user.php';</script>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Ekle</title>
    <?php require('../inc/links.php'); ?>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            overflow-x: hidden; /* Yatay kaydırmayı engeller */
        }
    </style>
</head>
<body>
    <?php require('../inc/header.php'); ?>
    <div class="row">
        <div class="col-12 text-center my-4">
            <h2>Kullanıcı Ekle</h2>
        </div>
    </div>
    
    <div class="container mt-5">
        <form method="POST" action="useradd.php">
            <div class="row">
                <!-- Ad Alanı -->
                <div class="col-lg-6 col-12 mb-3">
                    <div class="form-group">
                        <label for="lblAd">Ad</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                </div>

                <!-- Soyad Alanı -->
                <div class="col-lg-6 col-12 mb-3">
                    <div class="form-group">
                        <label for="lblSoyad">Soyad</label>
                        <input type="text" class="form-control" name="surname" required>
                    </div>
                </div>

                <!-- Kullanıcı Adı Alanı -->
                <div class="col-lg-6 col-12 mb-3">
                    <div class="form-group">
                        <label for="lblKullaniciAdi">Kullanıcı Adı</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                </div>

                <!-- Rol Alanı -->
                <div class="col-lg-6 col-12 mb-3">
                    <div class="form-group">
                        <label for="lblRol">Rol</label>
                        <select class="form-select" name="role" required>
                            <option value="1">Admin</option>
                            <option value="2">Kurye</option>
                        </select>
                    </div>
                </div>

                <!-- Telefon Numarası Alanı -->
                <div class="col-lg-6 col-12 mb-3">
                    <div class="form-group">
                        <label for="lblTelefonNumarasi">Telefon Numarası</label>
                        <input type="text" class="form-control" name="phone" required>
                    </div>
                </div>

                <!-- E-mail Alanı -->
                <div class="col-lg-6 col-12 mb-3">
                    <div class="form-group">
                        <label for="lblEmail">E-mail</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                </div>

                <!-- Şifre Alanı -->
                <div class="col-lg-6 col-12 mb-3">
                    <div class="form-group">
                        <label for="lblPassword">Şifre</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                </div>
            </div>

            <!-- Ekle Butonu -->
            <div class="row">
                <div class="col-12 d-flex justify-content-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">Ekle</button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
