<?php
include '../../../database/connectdb.php';

header('Content-Type: application/json'); // Указываем, что ответ будет в JSON формате

// Проверка на наличие POST-запроса
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_POST['user_id']; // ID пользователя, обычно берется из сессии или куки
    $productId = $_POST['product_id']; // ID продукта для добавления в корзину

    // Проверяем, есть ли уже такой продукт в корзине пользователя
    $checkQuery = "SELECT Id_basket FROM Basket WHERE User_id = ? AND id_product = ?";
    $stmt = $mysqli->prepare($checkQuery);
    $stmt->bind_param("ii", $userId, $productId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        // Добавляем продукт в корзину, если его там еще нет
        $insertQuery = "INSERT INTO Basket (User_id, id_product) VALUES (?, ?)";
        $stmt = $mysqli->prepare($insertQuery);
        $stmt->bind_param("ii", $userId, $productId);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            // Отправляем JSON ответ с успехом
            echo json_encode(['success' => true, 'message' => 'Продукт успешно добавлен в корзину']);
        } else {
            // Ошибка при добавлении продукта
            echo json_encode(['success' => false, 'message' => 'Ошибка при добавлении продукта в корзину']);
        }
    } else {
        // Продукт уже есть в корзине
        echo json_encode(['success' => false, 'message' => 'Этот продукт уже в вашей корзине']);
    }

    $stmt->close();
} else {
    // Некорректный тип запроса
    echo json_encode(['success' => false, 'message' => 'Ошибка: Данный запрос требует метод POST.']);
}

$mysqli->close();
?>
