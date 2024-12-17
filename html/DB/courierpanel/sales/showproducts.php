<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Satılan Ürünler</title>
    <?php require('../inc/links.php'); ?>
</head>
<body>
    <?php require('../inc/header.php'); ?>

    <div class="container mt-4">
        <h2>Satılan Ürünler</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Ürün ID</th>
                    <th>Ürün Adı</th>
                    <th>Fiyat</th>
                    <th>Adet</th>
                    <th>Satış Tarihi</th>
                </tr>
            </thead>
            <tbody>
    <tr>
        <td>1</td>
        <td>Pizza Margherita</td>
        <td>120.00</td>
        <td>2</td>
        <td>2024-12-10 18:45:00</td>
    </tr>
    <tr>
        <td>2</td>
        <td>Adana Kebap</td>
        <td>90.00</td>
        <td>1</td>
        <td>2024-12-11 12:30:00</td>
    </tr>
    <tr>
        <td>3</td>
        <td>Tavuk Döner</td>
        <td>45.00</td>
        <td>3</td>
        <td>2024-12-11 14:15:00</td>
    </tr>
    <tr>
        <td>4</td>
        <td>Lahmacun</td>
        <td>30.00</td>
        <td>5</td>
        <td>2024-12-12 13:00:00</td>
    </tr>
    <tr>
        <td>5</td>
        <td>Mantı</td>
        <td>75.00</td>
        <td>2</td>
        <td>2024-12-12 19:30:00</td>
    </tr>
</tbody>

        </table>
    </div>
</body>
</html>
