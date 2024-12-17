<?php
require('../dbcon.php'); // Veritabanı bağlantısı

// Kategorileri veritabanından çek
$categories = [];
$categoryQuery = "SELECT Category_ID, Category_Name FROM ProductCategory";
$categoryResult = mysqli_query($connection, $categoryQuery);
if ($categoryResult) {
    while ($row = mysqli_fetch_assoc($categoryResult)) {
        $categories[] = $row;
    }
}

// Ürünleri veritabanından çek
$products = [];
$productQuery = "SELECT Product_ID, Product_Name, Product_Price, Category_ID FROM Products";
$productResult = mysqli_query($connection, $productQuery);
if ($productResult) {
    while ($row = mysqli_fetch_assoc($productResult)) {
        $products[] = $row;
    }
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

// Ödeme yöntemlerini veritabanından çek
$paymentMethods = [];
$paymentMethodsQuery = "SELECT * FROM PaymentMethods";
$paymentMethodsResult = mysqli_query($connection, $paymentMethodsQuery);
if ($paymentMethodsResult) {
    while ($row = mysqli_fetch_assoc($paymentMethodsResult)) {
        $paymentMethods[] = $row;
    }
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



// Veritabanına sipariş ekle
$addSalesQuery = ""
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipariş Ekle</title>
    <?php require('../inc/links.php'); ?>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD7u8MJye2yoWklFvAgXFUFrS_ZO4pNjFo&libraries=places"></script>
</head>
<body>
    <?php require('../inc/header.php'); ?>

    <div class="container mt-5">
        <h2 class="mb-4 text-center">Sipariş Ekle</h2>
        <form action="insert_data.php" method="POST" id="orderForm">
            <!-- Ürün Seçimi -->
            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">Ürün Seçimi</div>
                <div class="card-body" id="productContainer">
                    <div class="product-row d-flex align-items-center mb-3">
                        <!-- Kategori Seçimi -->
                        <select class="form-select category-select me-2" required>
                            <option value="">Kategori Seçin</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['Category_ID']; ?>"><?php echo htmlspecialchars($category['Category_Name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <!-- Ürün Seçimi -->
                        <select class="form-select product-select me-2" name="productID[]" required>
                            <option value="">Ürün Seçin</option>
                        </select>
                        <!-- Miktar -->
                        <input type="number" class="form-control quantity me-2" name="quantity[]" min="1" value="1" required>
                        <!-- Sil Butonu -->
                        <button type="button" class="btn btn-danger remove-product">Sil</button>
                    </div>
                </div>
                <button type="button" id="addProductBtn" class="btn btn-success">Ürün Ekle</button>
            </div>

            <!-- Teslimat Konumu -->
            <!-- Teslimat Adresi -->
            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">Teslimat Adresi</div>
                <div class="card-body">
                    <input type="text" class="form-control mb-3" id="address" placeholder="Adres girin...">
                    <div id="map" style="height: 400px; margin-bottom: 20px;"></div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="latitude" class="form-label">Enlem (Latitude)</label>
                            <input type="text" class="form-control" id="latitude" name="latitude" readonly required>
                        </div>
                        <div class="col-md-6">
                            <label for="longitude" class="form-label">Boylam (Longitude)</label>
                            <input type="text" class="form-control" id="longitude" name="longitude" readonly required>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Ödeme Bilgileri -->
            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">Ödeme Bilgileri</div>
                <div class="card-body">
                    <label for="paymentMethod" class="form-label">Ödeme Yöntemi</label>
                    
                    <select class="form-select mb-3" id="paymentMethod" name="paymentMethod" required>
                        <option value="">Ödeme türü seçin</option>
                        <?php foreach ($paymentMethods as $method): ?>
                            <option value="<?php echo $method['PaymentMethod_ID']; ?>">
                                <?php echo htmlspecialchars($method['PaymentMethod_Name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <label for="paymentAmount" class="form-label">Ödeme Tutarı</label>
                    <input type="text" class="form-control" id="paymentAmount" name="paymentAmount" readonly>
                </div>
            </div>

            <!-- Kurye Seçimi -->
            <div class="card mb-3">
                <div class="card-header bg-dark text-white">Kurye Seçimi</div>
                <div class="card-body">
                    <label for="courierID" class="form-label">Kurye Seçin</label>
                    <select class="form-select" id="courierID" name="courierID" required>
                        <option value="">Kurye Seçin</option>
                        <?php foreach ($couriers as $courier): ?>
                            <option value="<?php echo $courier['Courier_ID']; ?>"><?php echo htmlspecialchars($courier['User_Name']); ?></option>
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
                    <option value="">Sipariş Durumu Seçin</option>
                    <?php foreach ($saleStatuses as $saleStatus): ?>
                        <option value="<?php echo $saleStatus['SaleStatu_ID']; ?>"><?php echo htmlspecialchars($saleStatus['SaleStatu_Name']); ?></option>
                    <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Ödeme durumu -->
            <!-- PaymentStatus_ID yi default olarak 2 al -->
            <input type="hidden" name="paymentStatusID" value="2">

            <!-- Toplam Fiyat -->
            <div class="mb-3">
                <label for="totalPrice" class="form-label">Toplam Fiyat</label>
                <input type="text" class="form-control" id="totalPrice" name="totalPrice" readonly>
            </div>

            

            <!-- Kaydet Butonu -->
            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-lg" name="add_sales" value="ADD">Siparişi Kaydet</button>
            </div>
        </form>
    </div>

    <!-- JS -->
    <script>
        const products = <?php echo json_encode($products); ?>;
        const productContainer = document.getElementById('productContainer');
        const addProductBtn = document.getElementById('addProductBtn');
        const totalPriceField = document.getElementById('totalPrice');
        const paymentAmountField = document.getElementById('paymentAmount');

        // Ürün Ekle Butonu
        addProductBtn.addEventListener('click', () => {
            const productRow = document.createElement('div');
            productRow.classList.add('product-row', 'd-flex', 'align-items-center', 'mb-3');
            productRow.innerHTML = `
                <select class="form-select category-select me-2" required>
                    <option value="">Kategori Seçin</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['Category_ID']; ?>"><?php echo htmlspecialchars($category['Category_Name']); ?></option>
                    <?php endforeach; ?>
                </select>
                <select class="form-select product-select me-2" name="productID[]" required>
                    <option value="">Ürün Seçin</option>
                </select>
                <input type="number" class="form-control quantity me-2" name="quantity[]" min="1" value="1" required>
                <button type="button" class="btn btn-danger remove-product">Sil</button>
            `;
            productContainer.appendChild(productRow);
        });

        // Dinamik Ürün ve Fiyat Güncelleme
        productContainer.addEventListener('change', (e) => {
            if (e.target.classList.contains('category-select')) {
                const productSelect = e.target.nextElementSibling;
                productSelect.innerHTML = '<option value="">Ürün Seçin</option>';
                products.forEach(product => {
                    if (product.Category_ID == e.target.value) {
                        productSelect.innerHTML += `<option value="${product.Product_ID}" data-price="${product.Product_Price}">${product.Product_Name} - ${product.Product_Price} ₺</option>`;
                    }
                });
            }
            updateTotalPrice();
        });

        // Toplam Fiyat Hesaplama
        function updateTotalPrice() {
            let total = 0;
            document.querySelectorAll('.product-row').forEach(row => {
                const productSelect = row.querySelector('.product-select');
                const quantity = row.querySelector('.quantity').value || 1;
                const price = productSelect.options[productSelect.selectedIndex]?.dataset.price || 0;
                total += parseFloat(price) * parseInt(quantity);
            });
            totalPriceField.value = total.toFixed(2) + ' ₺';
            paymentAmountField.value = total.toFixed(2) + ' ₺';
        }

        productContainer.addEventListener('input', updateTotalPrice);
        productContainer.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-product')) {
                e.target.parentElement.remove();
                updateTotalPrice();
            }
        });

        // Adres ve Harita
        // Google Maps API
        function initMap() {
            const map = new google.maps.Map(document.getElementById('map'), { center: { lat: 39.925533, lng: 32.866287 }, zoom: 13 });
            const marker = new google.maps.Marker({ map, draggable: true });
            const searchBox = new google.maps.places.SearchBox(document.getElementById('address'));

            searchBox.addListener('places_changed', () => {
                const place = searchBox.getPlaces()[0];
                if (!place) return;
                map.setCenter(place.geometry.location);
                marker.setPosition(place.geometry.location);
                document.getElementById('latitude').value = place.geometry.location.lat();
                document.getElementById('longitude').value = place.geometry.location.lng();
            });
        }
        window.onload = initMap;



        // adres harita bitiş
    </script>
</body>
</html>
