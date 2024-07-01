<?php
session_start();
include '../../database/connectdb.php';

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    header('Location: http://barsik/user/auth/auth.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Проверяем, что форма была отправлена
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные из формы
    $name = $_POST['userName'] ?? '';
    $email = $_POST['userEmail'] ?? '';
    $deliveryAddress = $_POST['userDeliveryAddress'] ?? '';

    // Проверяем, что данные не пустые
    if (empty($name) || empty($email) || empty($deliveryAddress)) {
        echo "Пожалуйста, заполните все поля.";
        exit;
    }

    // Подготовка запроса на обновление данных
    $updateQuery = "UPDATE users SET name = ?, Email = ?, contact_info = ? WHERE User_id = ?";
    $updateStmt = $mysqli->prepare($updateQuery);
    if ($updateStmt === false) {
        echo "Ошибка при подготовке запроса: " . $mysqli->error;
        exit;
    }

    // Привязка параметров к запросу
    $updateStmt->bind_param("sssi", $name, $email, $deliveryAddress, $userId);

    // Выполнение запроса
    if ($updateStmt->execute()) {
        // Редирект на страницу личного кабинета
        header('Location: ../personal_cab.php');
        exit;
    } else {
        echo "Ошибка при обновлении данных: " . $updateStmt->error;
    }

    // Закрытие запроса
    $updateStmt->close();
} else {
    echo "Некорректный метод запроса.";
}

$mysqli->close();
?>
