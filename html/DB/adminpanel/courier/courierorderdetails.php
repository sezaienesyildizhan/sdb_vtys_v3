<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipariş Detayı</title>
    <?php require('../inc/links.php'); ?>
</head>
<body>
    <?php require('../inc/header.php'); ?>
    <div class="container mt-5">
        <!-- Başlık -->
        <h2 class="mb-4 text-center">Sipariş Detayı</h2>

        <!-- Sipariş Bilgileri Kartı -->
        <div class="card mb-4">
            <div class="card-header bg-dark text-white">
                Sipariş Bilgileri
            </div>
            <div class="card-body">
                <p><strong>Sipariş ID:</strong> 101</p>
                <p><strong>Kurye Adı:</strong> Ali Yılmaz</p>
                <p><strong>Telefon:</strong> 555-123-4567</p>
                <p><strong>Sipariş Durumu:</strong> Teslim Edildi</p>
                <p><strong>Tarih:</strong> 2024-06-10</p>
                <p><strong>Toplam Tutar:</strong> 150 ₺</p>
            </div>
        </div>

        <!-- Ödeme Detayları -->
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">
                Ödeme Detayları
            </div>
            <div class="card-body">
                <p><strong>Ödeme Yöntemi:</strong> Kredi Kartı</p>
                <p><strong>Ödeme Durumu:</strong> Onaylandı</p>
                <p><strong>Ödeme Tarihi:</strong> 2024-06-10</p>
                <p><strong>Toplam Ödeme:</strong> 150 ₺</p>
                <p><strong>İşlem Numarası:</strong> #TRX20240610</p>
            </div>
        </div>

        <!-- Özet Tablosu -->
        <div class="card">
            <div class="card-header bg-secondary text-white">
                Sipariş Ürünleri
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Ürün ID</th>
                            <th>Ürün Adı</th>
                            <th>Miktar</th>
                            <th>Birim Fiyat</th>
                            <th>Toplam Fiyat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>201</td>
                            <td>Kahve</td>
                            <td>2</td>
                            <td>50 ₺</td>
                            <td>100 ₺</td>
                        </tr>
                        <tr>
                            <td>202</td>
                            <td>Kek</td>
                            <td>1</td>
                            <td>50 ₺</td>
                            <td>50 ₺</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>
