<?php
require_once '..\..\database\connectdb.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $product_id = $_POST['id'];

    // Подготавливаем запрос на удаление продукта
    $query = "DELETE FROM Product WHERE Id_product = ?";

    if ($stmt = $mysqli->prepare($query)) {
        $stmt->bind_param("i", $product_id);

        if ($stmt->execute()) {
            echo "Продукт успешно удален.";
            // Перенаправление пользователя обратно на страницу управления продуктами
            header("Location: ../index_admin.php");
            exit();
        } else {
            echo "Ошибка: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Ошибка: " . $mysqli->error;
    }

    $mysqli->close();
} else {
    echo "Некорректный запрос.";
}
?>
