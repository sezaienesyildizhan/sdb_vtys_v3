<?php require('../dbcon.php'); ?>

<?php 
$sale_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$saleItems = [];
if ($sale_id > 0) {
    // Satış verilerini çek
    $saleQuery = "
    SELECT Sales.*, Payment.Payment_Amount, Payment.PaymentMethod_ID, Payment.PaymentStatus_ID, 
           DeliveryLocation.DeliveryLocation_Latitude, DeliveryLocation.DeliveryLocation_Longitude
    FROM Sales
    LEFT JOIN Payment ON Sales.Sale_ID = Payment.Sale_ID
    LEFT JOIN DeliveryLocation ON Sales.Sale_ID = DeliveryLocation.Sale_ID
    WHERE Sales.Sale_ID = $sale_id";

    $saleResult = mysqli_query($connection, $saleQuery);
    $saleData = mysqli_fetch_assoc($saleResult);
    
    $totalPrice = isset($saleData['Payment_Amount']) ? $saleData['Payment_Amount'] : 0;
    // SaleItems verilerini çek
    $saleItemsQuery = "
    SELECT SaleItems.Product_ID, SaleItems.Quantity, Products.Product_Name, Products.Product_Price
    FROM SaleItems
    JOIN Products ON SaleItems.Product_ID = Products.Product_ID
    WHERE SaleItems.Sale_ID = $sale_id";

    $saleItemsResult = mysqli_query($connection, $saleItemsQuery);
    while ($item = mysqli_fetch_assoc($saleItemsResult)) {
        $saleItems[] = $item;
    }

    // Kategoriler
    $categories = [];
    $categoryQuery = "SELECT * FROM ProductCategory";
    $categoryResult = mysqli_query($connection, $categoryQuery);
    while($category = mysqli_fetch_assoc($categoryResult)) {
        $categories[] = $category;
    }

    // Ürünler
    $products = [];
    $productQuery = "SELECT Product_ID, Product_Name, Product_Price, Category_ID FROM Products";
    $productResult = mysqli_query($connection, $productQuery);
    while($product = mysqli_fetch_assoc($productResult)) {
        $products[] = $product;
    }

    // Kuryeleri veritabanından çek (sadece aktif olanlar)
    $couriers = [];
    $courierQuery = "
       SELECT Couriers.Courier_ID, Users.User_Name 
       FROM Couriers
       JOIN Users ON Couriers.User_ID = Users.User_ID
       WHERE Couriers.Courier_IsActive = 1";
    $courierResult = mysqli_query($connection, $courierQuery);

    if ($courierResult) {
       while ($row = mysqli_fetch_assoc($courierResult)) {
           $couriers[] = $row;
        }
    }

    // Ödeme Yöntemleri
    $paymentMethods = [];
    $paymentMethodsQuery = "SELECT * FROM PaymentMethods";
    $paymentMethodsResult = mysqli_query($connection, $paymentMethodsQuery);
    while($method = mysqli_fetch_assoc($paymentMethodsResult)) {
        $paymentMethods[] = $method;
    }

    // Sipariş durumlarını veritabanından çek
    $saleStatuses = [];
    $saleStatusesQuery = "SELECT * FROM SaleStatus;";
    $saleStatusesResult = mysqli_query($connection, $saleStatusesQuery);
    if ($saleStatusesResult) {
        while ($row = mysqli_fetch_assoc($saleStatusesResult)) {
            $saleStatuses[] = $row;
        }
    }

    // Ödeme durumlarını veritabanından çek
    $paymentStatuses = [];
    $paymentStatusesQuery = "SELECT * FROM PaymentStatus;";
    $paymentStatusesResult = mysqli_query($connection, $paymentStatusesQuery);
    if ($paymentStatusesResult) {
        while ($row = mysqli_fetch_assoc($paymentStatusesResult)) {
            $paymentStatuses[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sipariş Güncelle</title>
    <?php require('../inc/links.php'); ?>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD7u8MJye2yoWklFvAgXFUFrS_ZO4pNjFo&libraries=places"></script>
</head>
<body>
    <?php require('../inc/header.php'); ?>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Sipariş Güncelle</h2>

        <form action="savesale.php" method="POST">
            <input type="hidden" name="sale_id" value="<?= $sale_id ?>">

            <!-- Ürün Seçimi -->
            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">Ürün Seçimi</div>
                <div class="card-body" id="productContainer">
                    <?php foreach ($saleItems as $index => $item): ?>
                        <div class="product-row d-flex align-items-center mb-3">
                            <select class="form-select me-2" name="productID[]">
                                <?php foreach ($products as $product): ?>
                                    <option value="<?= $product['Product_ID'] ?>"
                                        <?= ($product['Product_ID'] == $item['Product_ID']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($product['Product_Name']) ?> - <?= $product['Product_Price'] ?> ₺
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <input type="number" class="form-control me-2" name="quantity[]" value="<?= $item['Quantity'] ?>" min="1" required>
                            <button type="button" class="btn btn-danger remove-product">Sil</button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" id="addProductBtn" class="btn btn-success">Ürün Ekle</button>
            </div>

            <!-- Teslimat Adresi -->
            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">Teslimat Adresi</div>
                <div class="card-body">
                    <!-- Adres Girişi -->
                    <input type="text" class="form-control mb-3" id="address" placeholder="Adres girin..." autocomplete="off">
                    <!-- Harita Alanı -->
                    <div id="map" style="height: 400px; margin-bottom: 20px;"></div>
                    <div class="row">
                        <!-- Latitude (Enlem) -->
                        <div class="col-md-6">
                            <label for="latitude" class="form-label">Enlem (Latitude)</label>
                            <input type="text" class="form-control" id="latitude" name="latitude" value="<?= $saleData['DeliveryLocation_Latitude'] ?? '' ?>" readonly required>
                        </div>
                        <!-- Longitude (Boylam) -->
                        <div class="col-md-6">
                            <label for="longitude" class="form-label">Boylam (Longitude)</label>
                            <input type="text" class="form-control" id="longitude" name="longitude" value="<?= $saleData['DeliveryLocation_Longitude'] ?? '' ?>" readonly required>
                        </div>
                    </div>
                    <button type="button" id="updateMapBtn" class="btn btn-info mt-3">Haritada Göster</button>
                </div>
            </div>


            <!-- Ödeme Bilgileri -->
            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">Ödeme Bilgileri</div>
                <div class="card-body">
                    <select class="form-select mb-3" name="paymentMethod" required>
                        <?php foreach ($paymentMethods as $method): ?>
                            <option value="<?= $method['PaymentMethod_ID'] ?>" 
                                <?= ($saleData['PaymentMethod_ID'] == $method['PaymentMethod_ID']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($method['PaymentMethod_Name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Kurye Seçimi -->
            <div class="card mb-3">
                <div class="card-header bg-dark text-white">Kurye Seçimi</div>
                <div class="card-body">
                    <select class="form-select" name="courierID" required>
                        <?php foreach ($couriers as $courier): ?>
                            <option value="<?= $courier['Courier_ID'] ?>" 
                                <?= ($saleData['Courier_ID'] == $courier['Courier_ID']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($courier['User_Name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Sipariş Durumu -->
            <div class="card mb-3">
                <div class="card-header bg-dark text-white">Sipariş Durumu</div>
                <div class="card-body">
                    <label for="saleStatusID" class="form-label">Sipariş Durumu</label>
                    <select class="form-select" id="saleStatusID" name="saleStatusID" required>
                    <option value="">Kurye Seçin</option>
                    <?php foreach ($saleStatuses as $saleStatus): ?>
                        <!-- <option value="<?php echo $saleStatus['SaleStatu_ID']; ?>"><?php echo htmlspecialchars($saleStatus['SaleStatu_Name']); ?></option> -->
                        <option value="<?= $saleStatus['SaleStatu_ID'] ?>" 
                            <?= ($saleData['SaleStatu_ID'] == $saleStatus['SaleStatu_ID']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($saleStatus['SaleStatu_Name']) ?>
                        </option>
                    <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Ödeme durumu -->
            <input type="hidden" name="paymentStatusID" value="<?= $saleData['PaymentStatus_ID'] ?? '' ?>">

            

            <!-- Kaydet Butonu -->
            <div class="text-center">
                <button type="submit" class="btn btn-primary">Siparişi Güncelle</button>
            </div>
        </form>
    </div>

    <script>
        let map, marker, autocomplete;

        // Toplam fiyatı hesaplayan fonksiyon
function updateTotalPrice() {
    let total = 0;

    // Tüm ürün satırlarını dolaş
    document.querySelectorAll('.product-row').forEach(row => {
        const select = row.querySelector('select'); // Ürün seçimi
        const priceText = select.selectedOptions[0].text; // Seçilen ürünün fiyatı içeren metni al
        const price = parseFloat(priceText.match(/(\d+(\.\d+)?)/)[0]); // Fiyatı çıkar
        const quantity = parseInt(row.querySelector('input[name="quantity[]"]').value) || 0; // Miktarı al

        total += price * quantity; // Toplam fiyatı hesapla
    });

    // Toplam fiyatı input alanına yaz
    document.getElementById('totalPrice1').value = total.toFixed(2) + ' ₺';
}

// Ürün satırı ekleme
document.getElementById('addProductBtn').addEventListener('click', function () {
    const productRow = `
        <div class="product-row d-flex align-items-center mb-3">
            <select class="form-select me-2 product-select" name="productID[]">
                <?php foreach ($products as $product): ?>
                    <option value="<?= $product['Product_ID'] ?>">
                        <?= htmlspecialchars($product['Product_Name']) ?> - <?= $product['Product_Price'] ?> ₺
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="number" class="form-control me-2 quantity" name="quantity[]" value="1" min="1" required>
            <button type="button" class="btn btn-danger remove-product">Sil</button>
        </div>
    `;
    document.getElementById('productContainer').insertAdjacentHTML('beforeend', productRow);

    // Toplam fiyatı güncelle
    updateTotalPrice();
});

// Ürün silme işlemi
document.getElementById('productContainer').addEventListener('click', function (e) {
    if (e.target.classList.contains('remove-product')) {
        e.target.closest('.product-row').remove(); // Ürün satırını sil
        updateTotalPrice(); // Toplam fiyatı güncelle
    }
});

// Ürün seçimi veya miktar değiştiğinde toplam fiyatı güncelle
document.getElementById('productContainer').addEventListener('input', function (e) {
    if (e.target.classList.contains('product-select') || e.target.classList.contains('quantity')) {
        updateTotalPrice(); // Toplam fiyatı güncelle
    }
});

    // Ürün veya miktar değiştiğinde toplam fiyat güncelle
    document.getElementById('productContainer').addEventListener('change', function (e) {
        if (e.target.classList.contains('product-select') || e.target.classList.contains('quantity')) {
            updateTotalPrice(); // Toplam fiyatı güncelle
        }
    });

    window.addEventListener('DOMContentLoaded', function () {
        updateTotalPrice();
    });


// Google Maps Entegrasyonu
function initMap() {
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const addressInput = document.getElementById('address');

    const defaultLat = parseFloat(latInput.value) || 39.9334;
    const defaultLng = parseFloat(lngInput.value) || 32.8597;
    const initialPosition = { lat: defaultLat, lng: defaultLng };

    map = new google.maps.Map(document.getElementById('map'), {
        center: initialPosition,
        zoom: 15
    });

    marker = new google.maps.Marker({
        position: initialPosition,
        map: map,
        draggable: true
    });

    // Marker taşındığında enlem ve boylam güncelle
    marker.addListener('dragend', function () {
        const newPosition = marker.getPosition();
        latInput.value = newPosition.lat().toFixed(6);
        lngInput.value = newPosition.lng().toFixed(6);
        updateAddressFromLatLng(newPosition);
    });

    autocomplete = new google.maps.places.Autocomplete(addressInput);
    autocomplete.bindTo('bounds', map);

    autocomplete.addListener('place_changed', function () {
        const place = autocomplete.getPlace();
        if (!place.geometry) return;

        const newPosition = place.geometry.location;
        latInput.value = newPosition.lat().toFixed(6);
        lngInput.value = newPosition.lng().toFixed(6);

        marker.setPosition(newPosition);
        map.panTo(newPosition);
    });

    function updateAddressFromLatLng(position) {
        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({ location: position }, (results, status) => {
            if (status === "OK" && results[0]) {
                addressInput.value = results[0].formatted_address;
            }
        });
    }

    document.getElementById('updateMapBtn').addEventListener('click', function () {
        const updatedLat = parseFloat(latInput.value) || defaultLat;
        const updatedLng = parseFloat(lngInput.value) || defaultLng;
        const updatedPosition = { lat: updatedLat, lng: updatedLng };

        marker.setPosition(updatedPosition);
        map.panTo(updatedPosition);
        updateAddressFromLatLng(updatedPosition);
    });
}

// Sayfa yüklendiğinde başlat
window.onload = function () {
    initMap();
    // updateTotalPrice(); // İlk toplam fiyatı güncelle
};


    </script>

</body>
</html>
