<?php


// Veritabanı bağlantısı
require_once('../dbcon.php'); // dbcon.php dosyasını dahil et

// ID parametresi ile kullanıcının bilgilerini çekme
if (isset($_GET['id'])) {
    $userID = $_GET['id'];

    // Kullanıcı bilgilerini veritabanından çekme
    $query = "SELECT * FROM Users WHERE User_ID = ?";
    $stmt = $connection->prepare($query);
    $stmt->bind_param("i", $userID); // 'i' integer parametreyi belirtir
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc(); // Kullanıcı verisini al
    } else {
        die("Kullanıcı bulunamadı.");
    }
} else {
    die("Geçersiz kullanıcı ID.");
}

// Güncelleme işlemi yapılacaksa
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Formdan gelen verileri al
    $userName = $_POST['User_Name'];
    $userSurname = $_POST['User_Surname'];
    $userUsername = $_POST['User_Username'];
    $userPhoneNumber = $_POST['User_PhoneNumber'];
    $userEmail = $_POST['User_Email'];
    $userPassword = $_POST['User_Password'];
    $userRole = $_POST['Role_ID']; // Role_ID'yi al
    
    // Veritabanını güncelleme
    $updateQuery = "UPDATE Users SET User_Name = ?, User_Surname = ?, User_Username = ?, User_PhoneNumber = ?, User_Email = ?, User_Password = ?, Role_ID = ? WHERE User_ID = ?";
    $updateStmt = $connection->prepare($updateQuery);
    $updateStmt->bind_param("ssssssii", $userName, $userSurname, $userUsername, $userPhoneNumber, $userEmail, $userPassword, $userRole, $userID);
    
    if ($updateStmt->execute()) {
        // Başarılı güncelleme sonrası yönlendirme
        echo "<script>window.location.href='/db/adminpanel/user.php';</script>";
        exit();
    } else {
        echo "Hata: " . $connection->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Güncelle</title>
    <?php require('../inc/links.php'); ?>
</head>
<body>

<?php require('../inc/header.php'); ?>

<div class="container mt-5">
    <h3 class="mb-3">Kullanıcı Güncelle</h3>

    <form method="POST">
        <div class="mb-3">
            <label for="User_Name" class="form-label">Ad</label>
            <input type="text" class="form-control" id="User_Name" name="User_Name" value="<?php echo $user['User_Name']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="User_Surname" class="form-label">Soyad</label>
            <input type="text" class="form-control" id="User_Surname" name="User_Surname" value="<?php echo $user['User_Surname']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="User_Username" class="form-label">Kullanıcı Adı</label>
            <input type="text" class="form-control" id="User_Username" name="User_Username" value="<?php echo $user['User_Username']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="User_PhoneNumber" class="form-label">Telefon Numarası</label>
            <input type="text" class="form-control" id="User_PhoneNumber" name="User_PhoneNumber" value="<?php echo $user['User_PhoneNumber']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="User_Email" class="form-label">E-posta</label>
            <input type="email" class="form-control" id="User_Email" name="User_Email" value="<?php echo $user['User_Email']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="User_Password" class="form-label">Şifre</label>
            <input type="password" class="form-control" id="User_Password" name="User_Password" value="<?php echo $user['User_Password']; ?>" required>
        </div>

        <!-- Rol seçimi için ComboBox -->
        <div class="mb-3">
            <label for="Role_ID" class="form-label">Rol</label>
            <select class="form-control" id="Role_ID" name="Role_ID" required>
                <?php
                // Rol tablosundaki tüm rolleri al
                $roleQuery = "SELECT Role_ID, Role_Name FROM Roles";
                $roleResult = $connection->query($roleQuery);
                
                if ($roleResult->num_rows > 0) {
                    while ($role = $roleResult->fetch_assoc()) {
                        $selected = ($role['Role_ID'] == $user['Role_ID']) ? "selected" : "";
                        echo "<option value='" . $role['Role_ID'] . "' $selected>" . $role['Role_Name'] . "</option>";
                    }
                } else {
                    echo "<option value=''>Rol Bulunamadı</option>";
                }
                ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Güncelle</button>
    </form>
</div>

</body>
</html>
