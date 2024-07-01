<?php
session_start();

include '../../database/connectdb.php';

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    header('Location: http://barsik/user/auth/auth.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Получаем данные пользователя, включая адрес доставки
$userQuery = "SELECT Bonus_points, contact_info FROM users WHERE User_id = ?";
$userStmt = $mysqli->prepare($userQuery);
$userStmt->bind_param("i", $userId);
$userStmt->execute();
$userResult = $userStmt->get_result();
$userData = $userResult->fetch_assoc();
$bonusPoints = $userData['Bonus_points'];
$deliveryAddress = $userData['contact_info']; // Поле адреса доставки

// Получаем товары из корзины пользователя
$query = "
    SELECT p.Name, p.Price, p.Description, p.Image, b.count
    FROM Basket b
    JOIN Product p ON b.id_product = p.Id_product
    WHERE b.User_id = ?
";

$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$products = [];
$totalPrice = 0;
while ($row = $result->fetch_assoc()) {
    $row['Total'] = $row['Price'] * $row['count'];
    $totalPrice += $row['Total'];
    $products[] = $row;
}

$stmt->close();
$userStmt->close();
$mysqli->close();
?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Оформление заказа</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>
<body>
<div class="container mt-4">
    <h2>Оформление заказа</h2>
    <div class="row">
        <?php foreach ($products as $product): ?>
        <div class="col-md-4">
            <div class="card mb-4">
                <img src="<?= $product['Image'] ?>" class="card-img-top" alt="<?= htmlspecialchars($product['Name']) ?>">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($product['Name']) ?></h5>
                    <p class="card-text"><?= htmlspecialchars($product['Description']) ?></p>
                    <p class="card-text">Цена: <?= htmlspecialchars($product['Price']) ?>₽ за единицу</p>
                    <p class="card-text">Количество: <?= $product['count'] ?></p>
                    <p class="card-text">Итого: <?= $product['Total'] ?>₽</p>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <h4>Общая стоимость: <?= $totalPrice ?>₽</h4>
    <form action="submit_order.php" method="post">
        <div class="mb-3">
            <label for="deliveryMethod" class="form-label">Способ доставки</label>
            <select id="deliveryMethod" name="deliveryMethod" class="form-select" onchange="toggleAddressField()">
                <option value="pickup">Самовывоз</option>
                <option value="courier">Доставка курьером</option>
                <option value="post">Почтовая доставка</option>
            </select>
        </div>
        <div class="mb-3" id="addressField" style="display: none;">
            <label for="deliveryAddress" class="form-label">Адрес доставки</label>
            <div class="input-group">
                <input type="text" id="deliveryAddress" name="deliveryAddress" class="form-control" value="<?= htmlspecialchars($deliveryAddress) ?>" readonly>
                <button class="btn btn-outline-secondary" type="button" onclick="window.location.href='../personal_cab.php'">Изменить</button>
            </div>
        </div>
        <div class="mb-3">
            <label for="payPoints" class="form-label">Оплатить баллами (Доступно: <?= $bonusPoints ?> баллов)</label>
            <input type="checkbox" id="payPoints" name="payPoints">
        </div>
        <button type="submit" class="btn btn-success">Оформить заказ</button><br><br>
    </form>
</div>

<script>
function toggleAddressField() {
    var deliveryMethod = document.getElementById('deliveryMethod').value;
    var addressField = document.getElementById('addressField');
    if (deliveryMethod === 'courier' || deliveryMethod === 'post') {
        addressField.style.display = 'block';
    } else {
        addressField.style.display = 'none';
    }
}
</script>


</body>
</html>

