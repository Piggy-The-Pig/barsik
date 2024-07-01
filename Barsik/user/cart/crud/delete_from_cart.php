<?php
session_start();
include '../../../database/connectdb.php';

$id_product = $_POST['id_product'];

// Удаление товара из корзины
$stmt = $mysqli->prepare("DELETE FROM Basket WHERE id_product = ? AND User_id = ?");
$stmt->bind_param("ii", $id_product, $_SESSION['user_id']);
$stmt->execute();
$stmt->close();

$mysqli->close();

// Возвращаем сигнал для перезагрузки страницы
echo json_encode(['success' => true]);
?>
