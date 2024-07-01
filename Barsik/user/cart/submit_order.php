<?php
session_start();
include '../../database/connectdb.php';

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    header('Location: http://barsik/user/auth/auth.php');
    exit;
}

$userId = $_SESSION['user_id'];
$deliveryMethod = $_POST['deliveryMethod'] ?? '';
$deliveryAddress = $_POST['deliveryAddress'] ?? '';
$useBonuses = isset($_POST['payPoints']) && $_POST['payPoints'] === 'on';  // Проверяем, использовал ли пользователь бонусы

// Если метод доставки - самовывоз, задаем фиксированный адрес
if ($deliveryMethod === 'pickup') {
    $deliveryAddress = 'Уфа, Уксивт, Пункт выдачи заказов.';
}

// Получаем текущие бонусные баллы пользователя
$currentBonusPointsQuery = "SELECT Bonus_points FROM users WHERE User_id = ?";
$bonusStmt = $mysqli->prepare($currentBonusPointsQuery);
$bonusStmt->bind_param("i", $userId);
$bonusStmt->execute();
$bonusResult = $bonusStmt->get_result();
$currentBonusPoints = $bonusResult->fetch_assoc()['Bonus_points'];

// Суммируем стоимость товаров в корзине
$query = "SELECT p.Price, b.count FROM Basket b JOIN Product p ON b.id_product = p.Id_product WHERE b.User_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$totalPrice = 0;
while ($row = $result->fetch_assoc()) {
    $totalPrice += $row['Price'] * $row['count'];
}

// Расчет использования бонусов
$maxBonusesToUse = $totalPrice * 0.30;  // Максимум 30% от стоимости заказа
$bonusesToUse = min($currentBonusPoints, $maxBonusesToUse);
$finalPrice = $totalPrice - $bonusesToUse;

// Начисление бонусов за заказ
$accruedBonuses = $finalPrice * 0.05;  // 5% от итоговой стоимости

// Подготовка статуса заказа в зависимости от выбранного метода доставки
$orderStatus = ($deliveryMethod === 'pickup') ? 'Обработка' : 'Доставляется';  // Измените логику при необходимости

// Создаем заказ
$insertOrder = "INSERT INTO Orders (User_id, Date_of_order, Status, Total_price, Used_bonuses, Accrued_bonuses, Delivery_address, Delivery_method) 
                VALUES (?, NOW(), ?, ?, ?, ?, ?, ?)";
$orderStmt = $mysqli->prepare($insertOrder);
$orderStmt->bind_param("isdddss", $userId, $orderStatus, $finalPrice, $bonusesToUse, $accruedBonuses, $deliveryAddress, $deliveryMethod);
$orderStmt->execute();
$orderId = $mysqli->insert_id;

// Переносим товары из корзины в Order_Product
$selectBasket = "SELECT id_product, count FROM Basket WHERE User_id = ?";
$insertOrderProduct = "INSERT INTO Order_Product (Id_order, Id_product, count) VALUES (?, ?, ?)";
$stmt = $mysqli->prepare($selectBasket);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$orderProductStmt = $mysqli->prepare($insertOrderProduct);
while ($row = $result->fetch_assoc()) {
    $orderProductStmt->bind_param("iii", $orderId, $row['id_product'], $row['count']);
    $orderProductStmt->execute();
}

// Очищаем корзину после создания заказа
$clearBasket = "DELETE FROM Basket WHERE User_id = ?";
$clearStmt = $mysqli->prepare($clearBasket);
$clearStmt->bind_param("i", $userId);
$clearStmt->execute();

// Обновляем бонусные баллы пользователя
$updateBonuses = "UPDATE users SET Bonus_points = Bonus_points - ? + ? WHERE User_id = ?";
$updateStmt = $mysqli->prepare($updateBonuses);
$updateStmt->bind_param("ddi", $bonusesToUse, $accruedBonuses, $userId);
$updateStmt->execute();

// Закрываем соединения
$stmt->close();
$orderStmt->close();
$orderProductStmt->close();
$clearStmt->close();
$updateStmt->close();
$mysqli->close();

header('Location: ../personal_cab.php');  // Перенаправляем на страницу успеха
exit;
?>
