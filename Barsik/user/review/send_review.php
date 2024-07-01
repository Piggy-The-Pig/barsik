<?php
session_start();
include '../../database/connectdb.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['user_id'])) {
        echo "Вы не авторизованы.";
        exit;
    }

    $userId = $_SESSION['user_id'];
    $productId = $_POST['id_product'] ?? null;
    $reviewText = $_POST['review_text'] ?? null;
    $rating = $_POST['rating'] ?? null;

    $errors = [];
    if (empty($productId)) {
        $errors[] = "ID продукта";
    } else {
        // Проверка существования ID продукта в базе данных
        $productCheck = $mysqli->prepare("SELECT * FROM Product WHERE Id_product = ?");
        $productCheck->bind_param("i", $productId);
        $productCheck->execute();
        $result = $productCheck->get_result();
        if ($result->num_rows === 0) {
            $errors[] = "ID продукта не существует";
        }
        $productCheck->close();
    }
    if (empty($reviewText)) {
        $errors[] = "Текст отзыва";
    }
    if (empty($rating)) {
        $errors[] = "Рейтинг";
    } else if ($rating < 1 || $rating > 5) {
        $errors[] = "Рейтинг должен быть числом от 1 до 5";
    }

    if (!empty($errors)) {
        echo "Необходимо заполнить следующие поля: " . implode(', ', $errors) . ".";
        exit;
    }

    // Подготовка запроса на вставку отзыва
    $stmt = $mysqli->prepare("INSERT INTO Reviews (user_id, id_product, review_text, rating, review_date) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("iisi", $userId, $productId, $reviewText, $rating);

    if ($stmt->execute()) {
        echo "Отзыв успешно добавлен!";
        header("Location: ../../index.php"); // Перенаправление после успешного добавления
    } else {
        echo "Ошибка при добавлении отзыва: " . $stmt->error; // Вывод ошибки
    }

    $stmt->close();
} else {
    echo "Ошибка запроса.";
}

$mysqli->close();
?>
