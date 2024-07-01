<?php
session_start();
include '../../../database/connectdb.php';

$id_product = $_POST['id_product'];
$count = max(1, intval($_POST['count'])); // Обеспечиваем, что количество не меньше 1

// Обновляем количество товара в корзине
$stmt = $mysqli->prepare("UPDATE Basket SET count = ? WHERE id_product = ? AND User_id = ?");
$stmt->bind_param("iii", $count, $id_product, $_SESSION['user_id']);
$stmt->execute();
$stmt->close();

// Вычисляем стоимость товара после изменения
$query = "SELECT Price FROM Product WHERE Id_product = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $id_product);
$stmt->execute();
$result = $stmt->get_result();
$product_price = $result->fetch_assoc()['Price'];
$total = $count * $product_price;

// Вычисляем общую стоимость всех товаров в корзине
$query = "SELECT SUM(p.Price * b.count) AS TotalPrice FROM Basket b JOIN Product p ON b.id_product = p.Id_product WHERE b.User_id = ?";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$totalPrice = $result->fetch_assoc()['TotalPrice'];

$mysqli->close();

// Возвращаем данные в формате JSON
echo json_encode(['total' => $total, 'totalPrice' => $totalPrice]);
?>
